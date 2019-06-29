<?php

namespace App\Tests\Balance\Model;

use App\Application\Model\User;
use App\Balance\Model\BalanceEntityInterface;
use App\Balance\Model\IncomeType;
use PHPUnit\Framework\TestCase;
use Ramsey\Uuid\UuidInterface;
use Symfony\Component\Security\Core\User\UserInterface;

final class IncomeTypeTest extends TestCase
{
    /** @var IncomeType $incomeType */
    private $incomeType;

    /** @var UserInterface $author */
    private $author;

    public function setUp()
    {
        $this->author = new User('Tester');
        $this->incomeType = new IncomeType('TestName', $this->author);
    }

    public function testClassImplementsBalanceEntityInterface()
    {
        $this->assertInstanceOf(BalanceEntityInterface::class, $this->incomeType);
    }

    public function testContractSetsInConstructor()
    {
        $this->assertInstanceOf(UuidInterface::class, $this->incomeType->getId());
        $this->assertSame('TestName', $this->incomeType->getName());
        $this->assertInstanceOf(UserInterface::class, $this->incomeType->getAuthor());
    }

    public function testToStringMethodGetsName()
    {
        ob_start();
        echo $this->incomeType;
        $toString = ob_get_clean();

        $this->assertSame('TestName', $toString);
    }

    public function testCanGetId()
    {
        $this->assertInstanceOf(UuidInterface::class, $this->incomeType->getId());
    }

    public function testCanGetName()
    {
        $this->assertSame('TestName', $this->incomeType->getName());
    }

    public function testCanChangeName()
    {
        $this->incomeType->changeName('SecondTestName');

        $this->assertSame('SecondTestName', $this->incomeType->getName());
    }

    public function testCanChangeDescription()
    {
        $this->incomeType->changeDescription('This is description');

        $this->assertSame('This is description', $this->incomeType->getDescription());
    }

    public function testCanCheckThatHasDescription()
    {
        $this->assertFalse($this->incomeType->hasDescription());

        $this->incomeType->changeDescription('This is description');

        $this->assertTrue($this->incomeType->hasDescription());
    }

    public function testCanGetAuthor()
    {
        $this->assertInstanceOf(UserInterface::class, $this->incomeType->getAuthor());
    }
}
