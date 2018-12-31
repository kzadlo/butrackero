<?php

namespace App\Tests\Balance\Model;

use App\Balance\Model\BalanceEntityInterface;
use App\Balance\Model\Income;
use App\Balance\Model\IncomeType;
use Doctrine\Common\Collections\Collection;
use PHPUnit\Framework\TestCase;

class IncomeTypeTest extends TestCase
{
    /** @var IncomeType $incomeType */
    private $incomeType;

    public function setUp()
    {
        $this->incomeType = new IncomeType();
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
        $this->incomeType->setName('TypeName');

        ob_start();
        echo $this->incomeType;
        $toString = ob_get_clean();

        $this->assertSame('TypeName', $toString);
    }

    public function testCanGetName()
    {
        $this->incomeType->setName('TypeName');

        $this->assertSame('TypeName', $this->incomeType->getName());
    }

    public function testCanGetDescription()
    {
        $this->incomeType->setDescription('This is description');

        $this->assertSame('This is description', $this->incomeType->getDescription());
    }

    public function testCanCheckThatHasDescription()
    {
        $this->assertFalse($this->incomeType->hasDescription());

        $this->incomeType->setDescription('This is description');
        $this->assertTrue($this->incomeType->hasDescription());
    }

    public function testCanGetIncomes()
    {
        $this->incomeType->addIncome($incomeOne = new Income());

        $this->assertContains($incomeOne, $this->incomeType->getIncomes());
    }

    public function testCanAddSeveralIncomes()
    {
        $this->incomeType->addIncome($incomeOne = new Income());
        $this->incomeType->addIncome(new Income());
        $this->incomeType->addIncome(new Income());

        $this->assertContains($incomeOne, $this->incomeType->getIncomes());
        $this->assertCount(3, $this->incomeType->getIncomes());
    }

    public function testCannotAddSameIncome()
    {
        $incomeOne = new Income();
        $this->incomeType->addIncome($incomeOne);
        $this->incomeType->addIncome($incomeOne);

        $this->assertCount(1, $this->incomeType->getIncomes());
    }

    public function testCanRemoveIncome()
    {
        $this->incomeType->addIncome($incomeOne = new Income());
        $this->incomeType->addIncome(new Income());

        $this->assertContains($incomeOne, $this->incomeType->getIncomes());

        $this->incomeType->removeIncome($incomeOne);
        $this->assertNotContains($incomeOne, $this->incomeType->getIncomes());
    }

    public function testCannotRemoveIncomeIfNotExist()
    {
        $this->assertCount(0, $this->incomeType->getIncomes());

        $this->incomeType->removeIncome(new Income());
        $this->assertCount(0, $this->incomeType->getIncomes());
    }

    public function testCanCheckThatHasConcreteIncome()
    {
        $incomeOne = new Income();

        $this->assertFalse($this->incomeType->hasIncome($incomeOne));

        $this->incomeType->addIncome($incomeOne);
        $this->assertTrue($this->incomeType->hasIncome($incomeOne));
    }

    public function testCanCheckThatHasIncomes()
    {
        $this->assertFalse($this->incomeType->hasIncomes());

        $this->incomeType->addIncome(new Income());
        $this->assertTrue($this->incomeType->hasIncomes());
    }
}