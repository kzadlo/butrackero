<?php

namespace App\Tests\Balance\Validator;

use App\Application\Model\User;
use App\Balance\Model\IncomeType;
use App\Balance\Validator\AbstractBalanceValidator;
use App\Balance\Validator\TypeValidator;
use App\Balance\Validator\ValidationInterface;
use PHPUnit\Framework\TestCase;

final class TypeValidatorTest extends TestCase
{
    /** @var TypeValidator $typeValidator */
    private $typeValidator;

    public function setUp()
    {
        $this->typeValidator = new TypeValidator();
    }

    public function testClassImplementsValidationInterface()
    {
        $this->assertInstanceOf(ValidationInterface::class, $this->typeValidator);
    }

    public function testClassExtendsAbstractBalanceValidator()
    {
        $this->assertInstanceOf(AbstractBalanceValidator::class, $this->typeValidator);
    }

    public function testValidate()
    {
        $this->typeValidator->validate(['name' => 'Test', 'description' => 'Test']);

        $this->assertTrue($this->typeValidator->isValid());

        $this->typeValidator->validate([]);
        $this->typeValidator->validate(['name' => 'Test']);
        $this->typeValidator->validate(['description' => 'Test']);

        $this->assertFalse($this->typeValidator->isValid());
    }

    public function testValidateTypeArray()
    {
        $this->assertFalse($this->typeValidator->validateTypeArray([]));
        $this->assertFalse($this->typeValidator->validateTypeArray(['name' => 'Test']));
        $this->assertFalse($this->typeValidator->validateTypeArray(['description' => 'Test']));
        $this->assertTrue($this->typeValidator->validateTypeArray(['name' => 0, 'description' => 1]));
    }

    public function testValidateName()
    {
        $this->assertFalse($this->typeValidator->validateName(null));
        $this->assertFalse($this->typeValidator->validateName('22'));
        $this->assertFalse($this->typeValidator->validateName(
            'Test that name has less than 128 characters. Test that name has less than 128 characters.
            Test that name has less than 128 characters. Test that name has less than 128 characters.'
        ));
        $this->assertTrue($this->typeValidator->validateName('Tes'));
    }

    public function testValidateDescription()
    {
        $this->assertFalse($this->typeValidator->validateDescription(
            'Test that description has less than 255 characters. Test that description has less than 255 characters.
            Test that description has less than 255 characters. Test that description has less than 255 characters.
            Test that description has less than 255 characters. Test that description has less than 255 characters.'
        ));
        $this->assertTrue($this->typeValidator->validateDescription(null));
        $this->assertTrue($this->typeValidator->validateDescription('T'));
    }

    public function testValidateTypeExists()
    {
        $type = new IncomeType(
            'TestObject',
            new User('TestUser')
        );

        $this->typeValidator->validateTypeExists($type);

        $this->assertNotContains(ValidationInterface::MESSAGE_OBJECT_NOT_EXISTS, $this->typeValidator->getErrors());

        $this->typeValidator->validateTypeExists(null);

        $this->assertContains(ValidationInterface::MESSAGE_OBJECT_NOT_EXISTS, $this->typeValidator->getErrors());
    }
}