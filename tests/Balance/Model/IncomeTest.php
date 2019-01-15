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

    public function setUp()
    {
        $this->income = new Income();
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
        $this->income->setAmount(50);

        $this->assertSame(50.00, $this->income->getAmount());
    }

    public function testCanGetType()
    {
        $type = new IncomeType();
        $this->income->setType($type);

        $this->assertSame($type, $this->income->getType());
    }

    public function testCanSetOnlyOneType()
    {
        $firstType = new IncomeType();
        $secondType = new IncomeType();
        $this->income->setType($firstType);
        $this->income->setType($secondType);

        $this->assertSame($secondType, $this->income->getType());
    }

    public function testCanGetCreated()
    {
        $this->income->setCreated(new \DateTime('2018-11-20'));

        $this->assertEquals(new \DateTime('2018-11-20'), $this->income->getCreated());
    }

    public function testCanGetAuthor()
    {
        $this->assertNull($this->income->getAuthor());

        $this->income->setAuthor(new User('Tester'));
        $this->assertInstanceOf(UserInterface::class, $this->income->getAuthor());
    }
}