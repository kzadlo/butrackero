<?php

namespace App\Tests\Balance\Validator;

use App\Application\Model\User;
use App\Balance\Model\Income;
use App\Balance\Model\IncomeType;
use App\Balance\Validator\AbstractBalanceValidator;
use App\Balance\Validator\IncomeValidator;
use App\Balance\Validator\ValidationInterface;
use PHPUnit\Framework\TestCase;

final class IncomeValidatorTest extends TestCase
{
    /** @var IncomeValidator $incomeValidator */
    private $incomeValidator;

    public function setUp()
    {
        $this->incomeValidator = new IncomeValidator();
    }

    public function testClassImplementsValidationInterface()
    {
        $this->assertInstanceOf(ValidationInterface::class, $this->incomeValidator);
    }

    public function testClassExtendsAbstractBalanceValidator()
    {
        $this->assertInstanceOf(AbstractBalanceValidator::class, $this->incomeValidator);
    }

    public function testValidate()
    {
        $this->incomeValidator->validate(['amount' => 12.34, 'type' => 'Test']);

        $this->assertTrue($this->incomeValidator->isValid());

        $this->incomeValidator->validate([]);
        $this->incomeValidator->validate(['amount' => 12.34]);
        $this->incomeValidator->validate(['type' => 'Test']);

        $this->assertFalse($this->incomeValidator->isValid());
    }

    public function testValidateIncomeArray()
    {
        $this->assertFalse($this->incomeValidator->validateIncomeArray([]));
        $this->assertFalse($this->incomeValidator->validateIncomeArray(['amount' => 12.34]));
        $this->assertFalse($this->incomeValidator->validateIncomeArray(['type' => 'Test']));
        $this->assertTrue($this->incomeValidator->validateIncomeArray(['amount' => 12.34, 'type' => 'Test']));
    }

    public function testValidateAmount()
    {
        $this->assertFalse($this->incomeValidator->validateAmount(null));
        $this->assertFalse($this->incomeValidator->validateAmount(-0.12));
        $this->assertTrue($this->incomeValidator->validateAmount(1.23));
    }

    public function testValidateType()
    {
        $this->assertFalse($this->incomeValidator->validateType(null));
        $this->assertTrue($this->incomeValidator->validateType('Type'));
    }

    public function testValidateTypeExists()
    {
        $type = new IncomeType(
            'TestName',
            new User('TestUser')
        );

        $this->incomeValidator->validateTypeExists($type);

        $this->assertNotContains(ValidationInterface::MESSAGE_OBJECT_NOT_EXISTS, $this->incomeValidator->getErrors());

        $this->incomeValidator->validateTypeExists(null);

        $this->assertContains(ValidationInterface::MESSAGE_OBJECT_NOT_EXISTS, $this->incomeValidator->getErrors());
    }

    public function testValidateIncomeExists()
    {
        $author = new User('TestUser');
        $income = new Income(
            100.10,
            new IncomeType('TestName', $author),
            $author
        );

        $this->incomeValidator->validateIncomeExists($income);

        $this->assertNotContains(ValidationInterface::MESSAGE_OBJECT_NOT_EXISTS, $this->incomeValidator->getErrors());

        $this->incomeValidator->validateIncomeExists(null);

        $this->assertContains(ValidationInterface::MESSAGE_OBJECT_NOT_EXISTS, $this->incomeValidator->getErrors());
    }
}
