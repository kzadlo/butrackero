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
        $this->assertSame(
            (new \DateTime())->format('Y-m-d H:i:s'),
            $this->expense->getCreated()->format('Y-m-d H:i:s')
        );
    }

    public function testCanGetAmount()
    {
        $this->expense->setAmount(50);

        $this->assertSame(50.00, $this->expense->getAmount());
    }

    public function testCanGetCategory()
    {
        $category = new ExpenseCategory();
        $this->expense->setCategory($category);

        $this->assertSame($category, $this->expense->getCategory());
    }

    public function testCanSetOnlyOneCategory()
    {
        $firstCategory = new ExpenseCategory();
        $secondCategory = new ExpenseCategory();
        $this->expense->setCategory($firstCategory);
        $this->expense->setCategory($secondCategory);

        $this->assertSame($secondCategory, $this->expense->getCategory());
    }

    public function testCanGetCreated()
    {
        $this->expense->setCreated(new \DateTime('2018-11-20'));

        $this->assertEquals(new \DateTime('2018-11-20'), $this->expense->getCreated());
    }

    public function testCanGetAuthor()
    {
        $this->assertNull($this->expense->getAuthor());

        $this->expense->setAuthor(new User('Tester'));
        $this->assertInstanceOf(UserInterface::class, $this->expense->getAuthor());
    }
}
