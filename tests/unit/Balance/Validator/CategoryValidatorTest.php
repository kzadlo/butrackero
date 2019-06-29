<?php

namespace App\Tests\Balance\Validator;

use App\Application\Model\User;
use App\Balance\Model\ExpenseCategory;
use App\Balance\Validator\AbstractBalanceValidator;
use App\Balance\Validator\CategoryValidator;
use App\Balance\Validator\ValidationInterface;
use PHPUnit\Framework\TestCase;

final class CategoryValidatorTest extends TestCase
{
    /** @var CategoryValidator $categoryValidator */
    private $categoryValidator;

    public function setUp()
    {
        $this->categoryValidator = new CategoryValidator();
    }

    public function testClassImplementsValidationInterface()
    {
        $this->assertInstanceOf(ValidationInterface::class, $this->categoryValidator);
    }

    public function testClassExtendsAbstractBalanceValidator()
    {
        $this->assertInstanceOf(AbstractBalanceValidator::class, $this->categoryValidator);
    }

    public function testValidate()
    {
        $this->categoryValidator->validate(['name' => 'Test', 'description' => 'Test']);

        $this->assertTrue($this->categoryValidator->isValid());

        $this->categoryValidator->validate([]);
        $this->categoryValidator->validate(['name' => 'Test']);
        $this->categoryValidator->validate(['description' => 'Test']);

        $this->assertFalse($this->categoryValidator->isValid());
    }

    public function testValidateCategoryArray()
    {
        $this->assertFalse($this->categoryValidator->validateCategoryArray([]));
        $this->assertFalse($this->categoryValidator->validateCategoryArray(['name' => 'Test']));
        $this->assertFalse($this->categoryValidator->validateCategoryArray(['description' => 'Test']));
        $this->assertTrue($this->categoryValidator->validateCategoryArray(['name' => 'Test', 'description' => 'Test']));
    }

    public function testValidateName()
    {
        $this->assertFalse($this->categoryValidator->validateName(null));
        $this->assertFalse($this->categoryValidator->validateName('22'));
        $this->assertFalse($this->categoryValidator->validateName(
            'Test that name has less than 128 characters. Test that name has less than 128 characters.
            Test that name has less than 128 characters. Test that name has less than 128 characters.'
        ));
        $this->assertTrue($this->categoryValidator->validateName('Tes'));
    }

    public function testValidateDescription()
    {
        $this->assertFalse($this->categoryValidator->validateDescription(
            'Test that description has less than 255 characters. Test that description has less than 255 characters.
            Test that description has less than 255 characters. Test that description has less than 255 characters.
            Test that description has less than 255 characters. Test that description has less than 255 characters.'
        ));
        $this->assertTrue($this->categoryValidator->validateDescription(null));
        $this->assertTrue($this->categoryValidator->validateDescription('T'));
    }

    public function testValidateCategoryExists()
    {
        $category = new ExpenseCategory(
            'TestObject',
            new User('TestUser')
        );

        $this->categoryValidator->validateCategoryExists($category);

        $this->assertNotContains(ValidationInterface::MESSAGE_OBJECT_NOT_EXISTS, $this->categoryValidator->getErrors());

        $this->categoryValidator->validateCategoryExists(null);

        $this->assertContains(ValidationInterface::MESSAGE_OBJECT_NOT_EXISTS, $this->categoryValidator->getErrors());
    }
}
