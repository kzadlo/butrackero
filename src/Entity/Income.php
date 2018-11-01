<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity()
 * @ORM\Table(name="income")
 */
class Income
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
     * @ORM\ManyToOne(targetEntity="IncomeType", inversedBy="incomes")
     * @ORM\JoinColumn(name="type_id", referencedColumnName="id", nullable=false)
     */
    private $type;

    /** @ORM\Column(type="datetime") */
    private $created;

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

    public function setAmount(float $amount): Income
    {
        $this->amount = $amount;
        return $this;
    }

    public function getType(): IncomeType
    {
        return $this->type;
    }

    public function setType(IncomeType $type): Income
    {
        $this->type = $type;
        return $this;
    }

    public function getCreated(): \DateTime
    {
        return $this->created;
    }

    public function setCreated(\DateTime $created): Income
    {
        $this->created = $created;
        return $this;
    }
}