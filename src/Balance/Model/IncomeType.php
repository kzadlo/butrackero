<?php

namespace App\Balance\Model;

use App\Application\Model\User;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Ramsey\Uuid\Uuid;
use Ramsey\Uuid\UuidInterface;
use Symfony\Component\Security\Core\User\UserInterface;

/**
 * @ORM\Entity(repositoryClass="App\Balance\Repository\IncomeTypeRepository")
 * @ORM\Table(name="income_type")
 */
class IncomeType implements BalanceEntityInterface
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

    /** @ORM\OneToMany(targetEntity="Income", mappedBy="type", cascade={"persist"}) */
    private $incomes;

    /**
     * @ORM\ManyToOne(targetEntity="App\Application\Model\User")
     * @ORM\JoinColumn(name="author_id", referencedColumnName="id", nullable=true)
     */
    private $author;

    public function __construct()
    {
        $this->id = Uuid::uuid4();
        $this->incomes = new ArrayCollection();
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

    public function getAuthor(): ?UserInterface
    {
        return $this->author;
    }

    public function setAuthor(User $author): IncomeType
    {
        $this->author = $author;
        return $this;
    }
}
