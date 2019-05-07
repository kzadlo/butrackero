<?php

namespace App\Tests\Balance\Model;

use App\Application\Model\User;
use App\Balance\Model\BalanceEntityInterface;
use App\Balance\Model\Income;
use App\Balance\Model\IncomeType;
use Doctrine\Common\Collections\Collection;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Security\Core\User\UserInterface;

class IncomeTypeTest extends TestCase
{
    /** @var IncomeType $incomeType */
    private $incomeType;

    /** @var UserInterface $author */
    private $author;

    public function setUp()
    {
        $this->author = new User('Tester');
        $this->incomeType = new IncomeType('TestName', $this->author);
    }

    public function testClassImplementsBalanceEntityInterface()
    {
        $this->assertInstanceOf(BalanceEntityInterface::class, $this->incomeType);
    }

    public function testIncomesSetInConstructorAreCollection()
    {
        $this->assertInstanceOf(Collection::class, $this->incomeType->getIncomes());
    }

    public function testToStringMethodGetsName()
    {
        ob_start();
        echo $this->incomeType;
        $toString = ob_get_clean();

        $this->assertSame('TestName', $toString);
    }

    public function testCanChangeName()
    {
        $this->incomeType->changeName('SecondTestName');

        $this->assertSame('SecondTestName', $this->incomeType->getName());
    }

    public function testCanGetName()
    {
        $this->assertSame('TestName', $this->incomeType->getName());
    }

    public function testCanGetDescription()
    {
        $this->incomeType->changeDescription('This is description');

        $this->assertSame('This is description', $this->incomeType->getDescription());
    }

    public function testCanCheckThatHasDescription()
    {
        $this->assertFalse($this->incomeType->hasDescription());
        $this->incomeType->changeDescription('This is description');

        $this->assertTrue($this->incomeType->hasDescription());
    }

    public function testCanGetIncomes()
    {
        $income = new Income(50.50, $this->incomeType, $this->author);

        $this->assertContains($income, $this->incomeType->getIncomes());
    }

    public function testCanAddSeveralIncomes()
    {
        new Income(50.50, $this->incomeType, $this->author);
        new Income(30.30, $this->incomeType, $this->author);

        $this->assertCount(2, $this->incomeType->getIncomes());
    }

    public function testCannotAddSameIncome()
    {
        $income = new Income(50.50, $this->incomeType, $this->author);
        $this->incomeType->addIncome($income);
        $this->incomeType->addIncome($income);

        $this->assertCount(1, $this->incomeType->getIncomes());
    }

    public function testCanRemoveIncome()
    {
        $income = new Income(50.50, $this->incomeType, $this->author);

        $this->assertContains($income, $this->incomeType->getIncomes());

        $this->incomeType->removeIncome($income);

        $this->assertNotContains($income, $this->incomeType->getIncomes());
    }

    public function testCanCheckThatHasConcreteIncome()
    {
        $income = new Income(50.50, $this->incomeType, $this->author);

        $this->assertTrue($this->incomeType->hasIncome($income));
    }

    public function testCanCheckThatHasIncomes()
    {
        new Income(50.50, $this->incomeType, $this->author);

        $this->assertTrue($this->incomeType->hasIncomes());
    }

    public function testCanGetAuthor()
    {
        $this->assertInstanceOf(UserInterface::class, $this->incomeType->getAuthor());
    }
}
