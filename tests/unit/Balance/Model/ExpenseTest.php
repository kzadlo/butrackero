<?php

namespace App\Tests\Balance\Model;

use App\Application\Model\User;
use App\Balance\Model\BalanceEntityInterface;
use App\Balance\Model\Expense;
use App\Balance\Model\ExpenseCategory;
use PHPUnit\Framework\TestCase;
use Ramsey\Uuid\UuidInterface;
use Symfony\Component\Security\Core\User\UserInterface;

final class ExpenseTest extends TestCase
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

    public function testContractSetsInConstructor()
    {
        $this->assertInstanceOf(UuidInterface::class, $this->expense->getId());
        $this->assertSame(100.10, $this->expense->getAmount());
        $this->assertInstanceOf(UserInterface::class, $this->expense->getAuthor());
        $this->assertInstanceOf(ExpenseCategory::class, $this->expense->getCategory());
        $this->assertInstanceOf(\DateTimeInterface::class, $this->expense->getCreated());
    }

    public function testCanGetId()
    {
        $this->assertInstanceOf(UuidInterface::class, $this->expense->getId());
    }

    public function testCanGetAmount()
    {
        $this->assertSame(100.10, $this->expense->getAmount());
    }

    public function testCanChangeAmount()
    {
        $this->expense->changeAmount(50.50);

        $this->assertSame(50.50, $this->expense->getAmount());
    }

    public function testCanGetCategory()
    {
        $this->assertInstanceOf(ExpenseCategory::class, $this->expense->getCategory());
    }

    public function testCanChangeCategory()
    {
        $category = new ExpenseCategory('TestChangedName', $this->author);
        $this->expense->changeCategory($category);

        $this->assertSame($category, $this->expense->getCategory());
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
