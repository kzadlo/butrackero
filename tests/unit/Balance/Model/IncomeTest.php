<?php

namespace App\Tests\Balance\Model;

use App\Application\Model\User;
use App\Balance\Model\BalanceEntityInterface;
use App\Balance\Model\Income;
use App\Balance\Model\IncomeType;
use PHPUnit\Framework\TestCase;
use Ramsey\Uuid\UuidInterface;
use Symfony\Component\Security\Core\User\UserInterface;

final class IncomeTest extends TestCase
{
    /** @var Income $income */
    private $income;

    /** @var UserInterface $author */
    private $author;

    public function setUp()
    {
        $this->author = new User('Tester');

        $this->income = new Income(
            100.10,
            new IncomeType('TestName', $this->author),
            $this->author
        );
    }

    public function testClassImplementsBalanceEntityInterface()
    {
        $this->assertInstanceOf(BalanceEntityInterface::class, $this->income);
    }

    public function testContractSetsInConstructor()
    {
        $this->assertInstanceOf(UuidInterface::class, $this->income->getId());
        $this->assertSame(100.10, $this->income->getAmount());
        $this->assertInstanceOf(UserInterface::class, $this->income->getAuthor());
        $this->assertInstanceOf(IncomeType::class, $this->income->getType());
        $this->assertInstanceOf(\DateTimeInterface::class, $this->income->getCreated());
    }

    public function testCanGetId()
    {
        $this->assertInstanceOf(UuidInterface::class, $this->income->getId());
    }

    public function testCanGetAmount()
    {
        $this->assertSame(100.10, $this->income->getAmount());
    }

    public function testCanChangeAmount()
    {
        $this->income->changeAmount(50.50);

        $this->assertSame(50.50, $this->income->getAmount());
    }

    public function testCanGetType()
    {
        $this->assertInstanceOf(IncomeType::class, $this->income->getType());
    }

    public function testCanChangeType()
    {
        $type = new IncomeType('TestChangedName', $this->author);
        $this->income->changeType($type);

        $this->assertSame($type, $this->income->getType());
    }

    public function testCanSetOnlyOneType()
    {
        $type = new IncomeType('SecondTestName', new User('Tester'));
        $this->income->changeType($type);

        $this->assertSame($type, $this->income->getType());
    }

    public function testCanGetCreated()
    {
        $this->assertInstanceOf(\DateTimeInterface::class, $this->income->getCreated());
    }

    public function testCanGetAuthor()
    {
        $this->assertInstanceOf(UserInterface::class, $this->income->getAuthor());
    }
}
