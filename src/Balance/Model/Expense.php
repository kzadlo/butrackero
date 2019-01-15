<?php

namespace App\Balance\Model;

use App\Application\Model\User;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Security\Core\User\UserInterface;

/**
 * @ORM\Entity(repositoryClass="App\Balance\Repository\ExpenseRepository")
 * @ORM\Table(name="expense")
 */
class Expense implements BalanceEntityInterface
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue(strategy="AUTO")
     * @ORM\Column(type="integer")
     */
    private $id;

    /** @ORM\Column(type="decimal", precision=11, scale=2) */
    private $amount;

    /**
     * @ORM\ManyToOne(targetEntity="ExpenseCategory", inversedBy="expenses")
     * @ORM\JoinColumn(name="category_id", referencedColumnName="id")
     */
    private $category;

    /** @ORM\Column(type="datetime") */
    private $created;

    /**
     * @ORM\ManyToOne(targetEntity="App\Application\Model\User")
     * @ORM\JoinColumn(name="author_id", referencedColumnName="id", nullable=true)
     */
    private $author;

    public function __construct()
    {
        $this->setCreated(new \DateTime());
    }

    public function getId(): int
    {
        return $this->id;
    }

    public function getAmount(): float
    {
        return $this->amount;
    }

    public function setAmount(float $amount): Expense
    {
        $this->amount = $amount;
        return $this;
    }

    public function getCategory(): ExpenseCategory
    {
        return $this->category;
    }

    public function setCategory(ExpenseCategory $category): Expense
    {
        $this->category = $category;
        $category->addExpense($this);
        return $this;
    }

    public function getCreated(): \DateTimeInterface
    {
        return $this->created;
    }

    public function setCreated(\DateTimeInterface $created): Expense
    {
        $this->created = $created;
        return $this;
    }

    public function getAuthor(): ?UserInterface
    {
        return $this->author;
    }

    public function setAuthor(User $author): Expense
    {
        $this->author = $author;
        return $this;
    }
}