<?php

namespace App\Balance\Model;

use Doctrine\ORM\Mapping as ORM;
use Ramsey\Uuid\Uuid;
use Ramsey\Uuid\UuidInterface;
use Symfony\Component\Security\Core\User\UserInterface;

/**
 * @ORM\Entity(repositoryClass="App\Balance\Repository\IncomeRepository")
 * @ORM\Table(name="income")
 */
class Income implements BalanceEntityInterface
{
    /**
     * @ORM\Id
     * @ORM\Column(type="uuid", unique=true)
     */
    private $id;

    /** @ORM\Column(type="decimal", precision=11, scale=2) */
    private $amount;

    /**
     * @ORM\ManyToOne(targetEntity="IncomeType", inversedBy="incomes")
     * @ORM\JoinColumn(name="type_id", referencedColumnName="id")
     */
    private $type;

    /** @ORM\Column(type="datetime") */
    private $created;

    /**
     * @ORM\ManyToOne(targetEntity="App\Application\Model\User")
     * @ORM\JoinColumn(name="author_id", referencedColumnName="id", nullable=true)
     */
    private $author;

    public function __construct(
        float $amount,
        IncomeType $type,
        UserInterface $author
    ) {
        $this->id = Uuid::uuid4();
        $this->changeAmount($amount);
        $this->changeType($type);
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

    public function changeAmount(float $amount): Income
    {
        $this->amount = $amount;
        return $this;
    }

    public function getType(): IncomeType
    {
        return $this->type;
    }

    public function changeType(IncomeType $type): Income
    {
        $this->type = $type;
        $type->addIncome($this);
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
