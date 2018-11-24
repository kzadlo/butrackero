<?php

namespace App\Balance\Model;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity()
 * @ORM\Table(name="expense_category")
 */
class ExpenseCategory implements BalanceEntityInterface
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue(strategy="AUTO")
     * @ORM\Column(type="integer")
     */
    private $id;

    /** @ORM\Column(type="string", length=128, unique=true) */
    private $name;

    /** @ORM\Column(type="string", length=255, nullable=true) */
    private $description;

    /** @ORM\OneToMany(targetEntity="Expense", mappedBy="category", cascade={"persist"}) */
    private $expenses;

    public function __construct()
    {
        $this->expenses = new ArrayCollection();
    }

    public function __toString(): string
    {
        return $this->getName();
    }

    public function getId(): int
    {
        return $this->id;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function setName(string $name): ExpenseCategory
    {
        $this->name = $name;
        return $this;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(string $description): ExpenseCategory
    {
        $this->description = $description;
        return $this;
    }

    public function hasDescription(): bool
    {
        return (bool) $this->getDescription();
    }

    public function getExpenses(): Collection
    {
        return $this->expenses;
    }

    public function addExpense(Expense $expense): ExpenseCategory
    {
        if (!$this->hasExpense($expense)) {
            $this->expenses->add($expense);
        }

        return $this;
    }

    public function removeExpense(Expense $expense): ExpenseCategory
    {
        if ($this->hasExpense($expense)) {
            $this->expenses->remove($expense);
        }

        return $this;
    }

    public function hasExpense(Expense $expense): bool
    {
        return $this->expenses->contains($expense);
    }

    public function hasExpenses(): bool
    {
        return !$this->expenses->isEmpty();
    }
}