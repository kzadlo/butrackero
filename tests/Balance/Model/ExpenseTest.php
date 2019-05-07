<?php

namespace App\Tests\Balance\Model;

use App\Application\Model\User;
use App\Balance\Model\BalanceEntityInterface;
use App\Balance\Model\Expense;
use App\Balance\Model\ExpenseCategory;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Security\Core\User\UserInterface;

class ExpenseTest extends TestCase
{
    /** @var Expense $expense */
    private $expense;

    /** @var UserInterface $author */
    private $author;

    public function setUp()
    {
        $this->author = new User('Tester');

        $this->expense = new Expense(
            100.10,
            new ExpenseCategory('TestName', $this->author),
            $this->author
        );
    }

    public function testClassImplementsBalanceEntityInterface()
    {
        $this->assertInstanceOf(BalanceEntityInterface::class, $this->expense);
    }

    public function testCreatedDateSetsInConstructorIsCurrent()
    {
        $this->assertInstanceOf(\DateTimeInterface::class, $this->expense->getCreated());
        $this->assertSame(
            (new \DateTime())->format('Y-m-d H:i:s'),
            $this->expense->getCreated()->format('Y-m-d H:i:s')
        );
    }

    public function testCanGetAmount()
    {
        $this->assertSame(100.10, $this->expense->getAmount());
    }

    public function testCanGetCategory()
    {
        $this->assertInstanceOf(ExpenseCategory::class, $this->expense->getCategory());
    }

    public function testCanSetOnlyOneCategory()
    {
        $category = new ExpenseCategory('SecondTestName', new User('Tester'));
        $this->expense->changeCategory($category);

        $this->assertSame($category, $this->expense->getCategory());
    }

    public function testCanGetCreated()
    {
        $this->assertInstanceOf(\DateTimeInterface::class, $this->expense->getCreated());
    }

    public function testCanGetAuthor()
    {
        $this->assertInstanceOf(UserInterface::class, $this->expense->getAuthor());
    }
}
