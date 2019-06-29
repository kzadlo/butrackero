<?php

namespace App\Tests\Balance\Validator;

use App\Application\Model\User;
use App\Balance\Model\Expense;
use App\Balance\Model\ExpenseCategory;
use App\Balance\Validator\AbstractBalanceValidator;
use App\Balance\Validator\ExpenseValidator;
use App\Balance\Validator\ValidationInterface;
use PHPUnit\Framework\TestCase;

final class ExpenseValidatorTest extends TestCase
{
    /** @var ExpenseValidator $expenseValidator */
    private $expenseValidator;

    public function setUp()
    {
        $this->expenseValidator = new ExpenseValidator();
    }

    public function testClassImplementsValidationInterface()
    {
        $this->assertInstanceOf(ValidationInterface::class, $this->expenseValidator);
    }

    public function testClassExtendsAbstractBalanceValidator()
    {
        $this->assertInstanceOf(AbstractBalanceValidator::class, $this->expenseValidator);
    }

    public function testValidate()
    {
        $this->expenseValidator->validate(['amount' => 12.34, 'category' => 'Test']);

        $this->assertTrue($this->expenseValidator->isValid());

        $this->expenseValidator->validate([]);
        $this->expenseValidator->validate(['amount' => 12.34]);
        $this->expenseValidator->validate(['category' => 'Test']);

        $this->assertFalse($this->expenseValidator->isValid());
    }

    public function testValidateExpenseArray()
    {
        $this->assertFalse($this->expenseValidator->validateExpenseArray([]));
        $this->assertFalse($this->expenseValidator->validateExpenseArray(['amount' => 12.34]));
        $this->assertFalse($this->expenseValidator->validateExpenseArray(['category' => 'Test']));
        $this->assertTrue($this->expenseValidator->validateExpenseArray(['amount' => 12.34, 'category' => 'Test']));
    }

    public function testValidateAmount()
    {
        $this->assertFalse($this->expenseValidator->validateAmount(null));
        $this->assertFalse($this->expenseValidator->validateAmount(-0.12));
        $this->assertTrue($this->expenseValidator->validateAmount(1.23));
    }

    public function testValidateCategory()
    {
        $this->assertFalse($this->expenseValidator->validateCategory(null));
        $this->assertTrue($this->expenseValidator->validateCategory('Category'));
    }

    public function testValidateCategoryExists()
    {
        $category = new ExpenseCategory(
            'TestName',
            new User('TestUser')
        );

        $this->expenseValidator->validateCategoryExists($category);

        $this->assertNotContains(ValidationInterface::MESSAGE_OBJECT_NOT_EXISTS, $this->expenseValidator->getErrors());

        $this->expenseValidator->validateCategoryExists(null);

        $this->assertContains(ValidationInterface::MESSAGE_OBJECT_NOT_EXISTS, $this->expenseValidator->getErrors());
    }

    public function testValidateExpenseExists()
    {
        $author = new User('TestUser');
        $expense = new Expense(
            100.10,
            new ExpenseCategory('TestName', $author),
            $author
        );

        $this->expenseValidator->validateExpenseExists($expense);

        $this->assertNotContains(ValidationInterface::MESSAGE_OBJECT_NOT_EXISTS, $this->expenseValidator->getErrors());

        $this->expenseValidator->validateExpenseExists(null);

        $this->assertContains(ValidationInterface::MESSAGE_OBJECT_NOT_EXISTS, $this->expenseValidator->getErrors());
    }
}
