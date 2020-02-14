<?php

namespace App\Tests\Balance\Model;

use App\Application\Model\User;
use App\Balance\Model\BalanceEntityInterface;
use App\Balance\Model\ExpenseCategory;
use PHPUnit\Framework\TestCase;
use Ramsey\Uuid\UuidInterface;
use Symfony\Component\Security\Core\User\UserInterface;

final class ExpenseCategoryTest extends TestCase
{
    /** @var ExpenseCategory $expenseCategory */
    private $expenseCategory;

    /** @var UserInterface $author */
    private $author;

    public function setUp(): void
    {
        $this->author = new User('Tester');

        $this->expenseCategory = new ExpenseCategory('TestName', $this->author);
    }

    public function testClassImplementsBalanceEntityInterface()
    {
        $this->assertInstanceOf(BalanceEntityInterface::class, $this->expenseCategory);
    }

    public function testContractSetsInConstructor()
    {
        $this->assertInstanceOf(UuidInterface::class, $this->expenseCategory->getId());
        $this->assertSame('TestName', $this->expenseCategory->getName());
        $this->assertInstanceOf(UserInterface::class, $this->expenseCategory->getAuthor());
    }

    public function testToStringMethodGetsName()
    {
        ob_start();
        echo $this->expenseCategory;
        $toString = ob_get_clean();

        $this->assertSame('TestName', $toString);
    }

    public function testCanGetId()
    {
        $this->assertInstanceOf(UuidInterface::class, $this->expenseCategory->getId());
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

    public function testCanGetAuthor()
    {
        $this->assertInstanceOf(UserInterface::class, $this->expenseCategory->getAuthor());
    }
}
