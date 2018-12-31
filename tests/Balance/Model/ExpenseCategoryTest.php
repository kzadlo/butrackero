<?php

namespace App\Tests\Balance\Model;

use App\Balance\Model\BalanceEntityInterface;
use App\Balance\Model\Expense;
use App\Balance\Model\ExpenseCategory;
use Doctrine\Common\Collections\Collection;
use PHPUnit\Framework\TestCase;

class ExpenseCategoryTest extends TestCase
{
    /** @var ExpenseCategory $expenseCategory */
    private $expenseCategory;

    public function setUp()
    {
        $this->expenseCategory = new ExpenseCategory();
    }

    public function testClassImplementsBalanceEntityInterface()
    {
        $this->assertInstanceOf(BalanceEntityInterface::class, $this->expenseCategory);
    }

    public function testExpensesSetInConstructorAreCollection()
    {
        $this->assertInstanceOf(Collection::class, $this->expenseCategory->getExpenses());
    }

    public function testToStringMethodGetsName()
    {
        $this->expenseCategory->setName('CategoryName');

        ob_start();
        echo $this->expenseCategory;
        $toString = ob_get_clean();

        $this->assertSame('CategoryName', $toString);
    }

    public function testCanGetName()
    {
        $this->expenseCategory->setName('CategoryName');

        $this->assertSame('CategoryName', $this->expenseCategory->getName());
    }

    public function testCanGetDescription()
    {
        $this->expenseCategory->setDescription('This is description');

        $this->assertSame('This is description', $this->expenseCategory->getDescription());
    }

    public function testCanCheckThatHasDescription()
    {
        $this->assertFalse($this->expenseCategory->hasDescription());

        $this->expenseCategory->setDescription('This is description');
        $this->assertTrue($this->expenseCategory->hasDescription());
    }

    public function testCanGetExpenses()
    {
        $this->expenseCategory->addExpense($expenseOne = new Expense());

        $this->assertContains($expenseOne, $this->expenseCategory->getExpenses());
    }

    public function testCanAddSeveralExpenses()
    {
        $this->expenseCategory->addExpense($expenseOne = new Expense());
        $this->expenseCategory->addExpense(new Expense());
        $this->expenseCategory->addExpense(new Expense());

        $this->assertContains($expenseOne, $this->expenseCategory->getExpenses());
        $this->assertCount(3, $this->expenseCategory->getExpenses());
    }

    public function testCannotAddSameExpense()
    {
        $expenseOne = new Expense();
        $this->expenseCategory->addExpense($expenseOne);
        $this->expenseCategory->addExpense($expenseOne);

        $this->assertCount(1, $this->expenseCategory->getExpenses());
    }

    public function testCanRemoveExpense()
    {
        $this->expenseCategory->addExpense($expenseOne = new Expense());
        $this->expenseCategory->addExpense(new Expense());

        $this->assertContains($expenseOne, $this->expenseCategory->getExpenses());

        $this->expenseCategory->removeExpense($expenseOne);
        $this->assertNotContains($expenseOne, $this->expenseCategory->getExpenses());
    }

    public function testCannotRemoveExpenseIfNotExist()
    {
        $this->assertCount(0, $this->expenseCategory->getExpenses());

        $this->expenseCategory->removeExpense(new Expense());
        $this->assertCount(0, $this->expenseCategory->getExpenses());
    }

    public function testCanCheckThatHasConcreteExpense()
    {
        $expenseOne = new Expense();

        $this->assertFalse($this->expenseCategory->hasExpense($expenseOne));

        $this->expenseCategory->addExpense($expenseOne);
        $this->assertTrue($this->expenseCategory->hasExpense($expenseOne));
    }

    public function testCanCheckThatHasExpenses()
    {
        $this->assertFalse($this->expenseCategory->hasExpenses());

        $this->expenseCategory->addExpense(new Expense());
        $this->assertTrue($this->expenseCategory->hasExpenses());
    }
}