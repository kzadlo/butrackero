<?php

namespace App\Tests\Balance\Model;

use App\Balance\Model\BalanceEntityInterface;
use App\Balance\Model\Income;
use App\Balance\Model\IncomeType;
use PHPUnit\Framework\TestCase;

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
        $this->assertEquals(
            (new \DateTime())->format('Y-m-d H:i:s'),
            $this->income->getCreated()->format('Y-m-d H:i:s')
        );
    }

    public function testCanGetAmount()
    {
        $this->income->setAmount(50);

        $this->assertEquals($this->income->getAmount(), 50.00);
    }

    public function testCanGetType()
    {
        $type = new IncomeType();
        $this->income->setType($type);

        $this->assertEquals($this->income->getType(), $type);
    }

    public function testCanSetOnlyOneType()
    {
        $firstType = new IncomeType();
        $secondType = new IncomeType();
        $this->income->setType($firstType);
        $this->income->setType($secondType);

        $this->assertEquals($this->income->getType(), $secondType);
    }

    public function testCanGetCreated()
    {
        $this->income->setCreated(new \DateTime('2018-11-20'));

        $this->assertEquals($this->income->getCreated(), new \DateTime('2018-11-20'));
    }
}