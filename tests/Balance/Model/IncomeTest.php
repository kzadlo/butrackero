<?php

namespace App\Tests\Balance\Model;

use App\Application\Model\User;
use App\Balance\Model\BalanceEntityInterface;
use App\Balance\Model\Income;
use App\Balance\Model\IncomeType;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Security\Core\User\UserInterface;

class IncomeTest extends TestCase
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

    public function testCreatedDateSetsInConstructorIsCurrent()
    {
        $this->assertInstanceOf(\DateTimeInterface::class, $this->income->getCreated());
        $this->assertSame(
            (new \DateTime())->format('Y-m-d H:i:s'),
            $this->income->getCreated()->format('Y-m-d H:i:s')
        );
    }

    public function testCanGetAmount()
    {
        $this->assertSame(100.10, $this->income->getAmount());
    }

    public function testCanGetType()
    {
        $this->assertInstanceOf(IncomeType::class, $this->income->getType());
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
