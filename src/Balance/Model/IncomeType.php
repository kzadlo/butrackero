<?php

namespace App\Balance\Model;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Balance\Repository\IncomeTypeRepository")
 * @ORM\Table(name="income_type")
 */
class IncomeType implements BalanceEntityInterface
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

    /** @ORM\OneToMany(targetEntity="Income", mappedBy="type", cascade={"persist"}) */
    private $incomes;

    public function __construct()
    {
        $this->incomes = new ArrayCollection();
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

    public function setName(string $name): IncomeType
    {
        $this->name = $name;
        return $this;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(string $description): IncomeType
    {
        $this->description = $description;
        return $this;
    }

    public function hasDescription(): bool
    {
        return (bool) $this->getDescription();
    }

    public function getIncomes(): Collection
    {
        return $this->incomes;
    }

    public function addIncome(Income $income): IncomeType
    {
        if (!$this->hasIncome($income)) {
            $this->incomes->add($income);
        }

        return $this;
    }

    public function removeIncome(Income $income): IncomeType
    {
        if ($this->hasIncome($income)) {
            $this->incomes->removeElement($income);
        }

        return $this;
    }

    public function hasIncome(Income $income): bool
    {
        return $this->incomes->contains($income);
    }

    public function hasIncomes(): bool
    {
        return !$this->incomes->isEmpty();
    }
}