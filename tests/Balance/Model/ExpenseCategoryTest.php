<?php

namespace App\Tests\Balance\Model;

use App\Application\Model\User;
use App\Balance\Model\BalanceEntityInterface;
use App\Balance\Model\Expense;
use App\Balance\Model\ExpenseCategory;
use Doctrine\Common\Collections\Collection;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Security\Core\User\UserInterface;

class ExpenseCategoryTest extends TestCase
{
    /** @var ExpenseCategory $expenseCategory */
    private $expenseCategory;

    /** @var UserInterface $author */
    private $author;

    public function setUp()
    {
        $this->author = new User('Tester');

        $this->expenseCategory = new ExpenseCategory('TestName', $this->author);
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
        ob_start();
        echo $this->expenseCategory;
        $toString = ob_get_clean();

        $this->assertSame('TestName', $toString);
    }

    public function testCanChangeName()
    {
        $this->expenseCategory->changeName('SecondTestName');

        $this->assertSame('SecondTestName', $this->expenseCategory->getName());
    }

    public function testCanGetName()
    {
        $this->assertSame('TestName', $this->expenseCategory->getName());
    }

    public function testCanGetDescription()
    {
        $this->expenseCategory->changeDescription('This is description');

        $this->assertSame('This is description', $this->expenseCategory->getDescription());
    }

    public function testCanCheckThatHasDescription()
    {
        $this->assertFalse($this->expenseCategory->hasDescription());

        $this->expenseCategory->changeDescription('This is description');
        $this->assertTrue($this->expenseCategory->hasDescription());
    }

    public function testCanGetExpenses()
    {
        $expense = new Expense(
            50.50,
            $this->expenseCategory,
            $this->author
        );

        $this->assertContains($expense, $this->expenseCategory->getExpenses());
    }

    public function testCanAddSeveralExpenses()
    {
        new Expense(
            50.50,
            $this->expenseCategory,
            $this->author
        );

        new Expense(
            30.30,
            $this->expenseCategory,
            $this->author
        );

        $this->assertCount(2, $this->expenseCategory->getExpenses());
    }

    public function testCannotAddSameExpense()
    {
        $expense = new Expense(50.50, $this->expenseCategory, $this->author);
        $this->expenseCategory->addExpense($expense);
        $this->expenseCategory->addExpense($expense);

        $this->assertCount(1, $this->expenseCategory->getExpenses());
    }

    public function testCanRemoveExpense()
    {
        $expense = new Expense(50.50, $this->expenseCategory, $this->author);

        $this->assertContains($expense, $this->expenseCategory->getExpenses());

        $this->expenseCategory->removeExpense($expense);

        $this->assertNotContains($expense, $this->expenseCategory->getExpenses());
    }

    public function testCanCheckThatHasConcreteExpense()
    {
        $expense = new Expense(50.50, $this->expenseCategory, $this->author);

        $this->assertTrue($this->expenseCategory->hasExpense($expense));
    }

    public function testCanCheckThatHasExpenses()
    {
        new Expense(50.50, $this->expenseCategory, $this->author);

        $this->assertTrue($this->expenseCategory->hasExpenses());
    }

    public function testCanGetAuthor()
    {
        $this->assertInstanceOf(UserInterface::class, $this->expenseCategory->getAuthor());
    }
}
