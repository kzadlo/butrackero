<?php

namespace App\Tests\Balance\Validator;

use App\Application\Model\User;
use App\Balance\Model\ExpenseCategory;
use App\Balance\Validator\AbstractBalanceValidator;
use App\Balance\Validator\ValidationInterface;
use PHPUnit\Framework\TestCase;

class AbstractBalanceValidatorTest extends TestCase
{
    /** @var AbstractBalanceValidator $balanceValidator */
    private $balanceValidator;

    public function setUp()
    {
        $this->balanceValidator = $this
            ->getMockBuilder(AbstractBalanceValidator::class)
            ->getMockForAbstractClass();
    }

    public function testClassImplementsValidationInterface()
    {
        $this->assertInstanceOf(ValidationInterface::class, $this->balanceValidator);
    }

    public function testIsObjectExists()
    {
        $balanceObject = new ExpenseCategory(
            'TestObject',
            new User('TestUser')
        );

        $this->assertFalse($this->balanceValidator->isObjectExists(null));
        $this->assertTrue($this->balanceValidator->isObjectExists($balanceObject));
    }

    public function testIsArrayEmpty()
    {
        $this->assertFalse($this->balanceValidator->isArrayEmpty([1]));
        $this->assertTrue($this->balanceValidator->isArrayEmpty([]));
    }

    public function testHasArrayKey()
    {
        $array = ['yes' => 1];

        $this->assertFalse($this->balanceValidator->hasArrayKey('not', $array));
        $this->assertTrue($this->balanceValidator->hasArrayKey('yes', $array));
    }

    public function testIsGreaterThanZero()
    {
        $this->assertFalse($this->balanceValidator->isGreaterThanZero(-0.33));
        $this->assertFalse($this->balanceValidator->isGreaterThanZero(0.00));
        $this->assertTrue($this->balanceValidator->isGreaterThanZero(0.01));
    }

    public function testIsNull()
    {
        $this->assertFalse($this->balanceValidator->isNull(0));
        $this->assertFalse($this->balanceValidator->isNull(0.00));
        $this->assertFalse($this->balanceValidator->isNull([]));
        $this->assertFalse($this->balanceValidator->isNull(''));
        $this->assertTrue($this->balanceValidator->isNull(null));
    }

    public function testIsFloat()
    {
        $this->assertFalse($this->balanceValidator->isFloat(0));
        $this->assertFalse($this->balanceValidator->isFloat(null));
        $this->assertFalse($this->balanceValidator->isFloat([]));
        $this->assertFalse($this->balanceValidator->isFloat('String'));
        $this->assertTrue($this->balanceValidator->isFloat(0.00));
    }

    public function testIsInt()
    {
        $this->assertFalse($this->balanceValidator->isInt(0.00));
        $this->assertFalse($this->balanceValidator->isInt(null));
        $this->assertFalse($this->balanceValidator->isInt([]));
        $this->assertFalse($this->balanceValidator->isInt('String'));
        $this->assertTrue($this->balanceValidator->isInt(0));
    }

    public function testIsString()
    {
        $this->assertFalse($this->balanceValidator->isString(1));
        $this->assertFalse($this->balanceValidator->isString(1.23));
        $this->assertFalse($this->balanceValidator->isString([]));
        $this->assertFalse($this->balanceValidator->isString(null));
        $this->assertTrue($this->balanceValidator->isString('String'));
    }

    public function testIsShorterThan()
    {
        $this->assertFalse($this->balanceValidator->isShorterThan(null, 0));
        $this->assertFalse($this->balanceValidator->isShorterThan('String', 6));
        $this->assertTrue($this->balanceValidator->isShorterThan('String', 7));
        $this->assertTrue($this->balanceValidator->isShorterThan(null, 1));
    }

    public function testIsLongerThan()
    {
        $this->assertFalse($this->balanceValidator->isLongerThan('String', 7));
        $this->assertFalse($this->balanceValidator->isLongerThan(null, 1));
        $this->assertFalse($this->balanceValidator->isLongerThan(null, 0));
        $this->assertTrue($this->balanceValidator->isLongerThan(null, -1));
        $this->assertTrue($this->balanceValidator->isLongerThan('String', 5));
    }
}