<?php

namespace App\Tests\Balance\Model;

use App\Balance\Model\BalanceEntityInterface;
use App\Balance\Model\Expense;
use App\Balance\Model\ExpenseCategory;
use PHPUnit\Framework\TestCase;

class ExpenseTest extends TestCase
{
    /** @var Expense $expense */
    private $expense;

    public function setUp()
    {
        $this->expense = new Expense();
    }

    public function testClassImplementsBalanceEntityInterface()
    {
        $this->assertInstanceOf(BalanceEntityInterface::class, $this->expense);
    }

    public function testCreatedDateSetsInConstructorIsCurrent()
    {
        $this->assertInstanceOf(\DateTimeInterface::class, $this->expense->getCreated());
        $this->assertEquals(
            (new \DateTime())->format('Y-m-d H:i:s'),
            $this->expense->getCreated()->format('Y-m-d H:i:s')
        );
    }

    public function testCanGetAmount()
    {
        $this->expense->setAmount(50);

        $this->assertEquals($this->expense->getAmount(), 50.00);
    }

    public function testCanGetCategory()
    {
        $category = new ExpenseCategory();
        $this->expense->setCategory($category);

        $this->assertEquals($this->expense->getCategory(), $category);
    }

    public function testCanSetOnlyOneCategory()
    {
        $firstCategory = new ExpenseCategory();
        $secondCategory = new ExpenseCategory();
        $this->expense->setCategory($firstCategory);
        $this->expense->setCategory($secondCategory);

        $this->assertEquals($this->expense->getCategory(), $secondCategory);
    }

    public function testCanGetCreated()
    {
        $this->expense->setCreated(new \DateTime('2018-11-20'));

        $this->assertEquals($this->expense->getCreated(), new \DateTime('2018-11-20'));
    }
}