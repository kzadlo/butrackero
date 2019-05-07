<?php

namespace App\Balance\Model;

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

    public function changeName(string $name): IncomeType
    {
        $this->name = $name;
        return $this;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function changeDescription(string $description): IncomeType
    {
        $this->description = $description;
        return $this;
    }

    public function hasDescription(): bool
    {
        return (bool) $this->getDescription();
    }

    public function getAuthor(): ?UserInterface
    {
        return $this->author;
    }
}
