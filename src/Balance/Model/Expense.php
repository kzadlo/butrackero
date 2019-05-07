<?php

namespace App\Balance\Model;

use Doctrine\ORM\Mapping as ORM;
use Ramsey\Uuid\Uuid;
use Ramsey\Uuid\UuidInterface;
use Symfony\Component\Security\Core\User\UserInterface;

/**
 * @ORM\Entity(repositoryClass="App\Balance\Repository\ExpenseRepository")
 * @ORM\Table(name="expense")
 */
class Expense implements BalanceEntityInterface
{
    /**
     * @ORM\Id
     * @ORM\Column(type="uuid", unique=true)
     */
    private $id;

    /** @ORM\Column(type="decimal", precision=11, scale=2) */
    private $amount;

    /**
     * @ORM\ManyToOne(targetEntity="ExpenseCategory")
     * @ORM\JoinColumn(name="category_id", referencedColumnName="id")
     */
    private $category;

    /** @ORM\Column(type="datetime") */
    private $created;

    /**
     * @ORM\ManyToOne(targetEntity="App\Application\Model\User")
     * @ORM\JoinColumn(name="author_id", referencedColumnName="id")
     */
    private $author;

    public function __construct(
        float $amount,
        ExpenseCategory $category,
        UserInterface $author
    ) {
        $this->id = Uuid::uuid4();
        $this->changeAmount($amount);
        $this->changeCategory($category);
        $this->author = $author;
        $this->created = new \DateTime();
    }

    public function getId(): UuidInterface
    {
        return $this->id;
    }

    public function getAmount(): float
    {
        return $this->amount;
    }

    public function changeAmount(float $amount): Expense
    {
        $this->amount = $amount;
        return $this;
    }

    public function getCategory(): ExpenseCategory
    {
        return $this->category;
    }

    public function changeCategory(ExpenseCategory $category): Expense
    {
        $this->category = $category;
        return $this;
    }

    public function getCreated(): \DateTimeInterface
    {
        return $this->created;
    }

    public function getAuthor(): ?UserInterface
    {
        return $this->author;
    }
}
