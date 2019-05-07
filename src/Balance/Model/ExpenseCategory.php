<?php

namespace App\Balance\Model;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Ramsey\Uuid\Uuid;
use Ramsey\Uuid\UuidInterface;
use Symfony\Component\Security\Core\User\UserInterface;

/**
 * @ORM\Entity(repositoryClass="App\Balance\Repository\ExpenseCategoryRepository")
 * @ORM\Table(name="expense_category")
 */
class ExpenseCategory implements BalanceEntityInterface
{
    /**
     * @ORM\Id
     * @ORM\Column(type="uuid", unique=true)
     */
    private $id;

    /** @ORM\Column(type="string", length=128, unique=true) */
    private $name;

    /** @ORM\Column(type="string", length=255, nullable=true) */
    private $description;

    /** @ORM\OneToMany(targetEntity="Expense", mappedBy="category", cascade={"persist"}) */
    private $expenses;

    /**
     * @ORM\ManyToOne(targetEntity="App\Application\Model\User")
     * @ORM\JoinColumn(name="author_id", referencedColumnName="id", nullable=true)
     */
    private $author;

    public function __construct(
        string $name,
        UserInterface $author
    ) {
        $this->id = Uuid::uuid4();
        $this->changeName($name);
        $this->author = $author;
        $this->expenses = new ArrayCollection();
    }

    public function __toString(): string
    {
        return $this->getName();
    }

    public function getId(): UuidInterface
    {
        return $this->id;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function changeName(string $name): ExpenseCategory
    {
        $this->name = $name;
        return $this;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function changeDescription(string $description): ExpenseCategory
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
            $this->expenses->removeElement($expense);
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

    public function getAuthor(): ?UserInterface
    {
        return $this->author;
    }
}
