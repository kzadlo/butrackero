<?php

namespace App\Application\Model;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Security\Core\User\UserInterface;

/**
 * @ORM\Entity(repositoryClass="App\Application\Repository\UserRepository")
 * @ORM\Table(name="user")
 */
class User implements UserInterface
{
    const ACTIVE_USER = 1;
    const INACTIVE_USER = 0;

    /**
     * @ORM\Id()
     * @ORM\GeneratedValue(strategy="AUTO")
     * @ORM\Column(type="integer")
     */
    private $id;

    /** @ORM\Column(type="string", unique=true) */
    private $username;

    /** @ORM\Column(type="string") */
    private $password;

    /** @ORM\Column(type="string") */
    private $active;

    public function __construct(string $username)
    {
        $this->username = $username;

        $this->activate();
    }

    public function getId(): int
    {
        return $this->id;
    }

    public function getUsername(): string
    {
        return $this->username;
    }

    public function setUsername(string $username): UserInterface
    {
        $this->username = $username;
        return $this;
    }

    public function getPassword(): string
    {
        return $this->password;
    }

    public function setPassword(string $hashedPassword): UserInterface
    {
        $this->password = $hashedPassword;
        return $this;
    }

    public function getRoles(): array
    {
        return ['ROLE_USER'];
    }

    public function isActive(): bool
    {
        return $this->active;
    }

    public function activate(): UserInterface
    {
        $this->active = self::ACTIVE_USER;
        return $this;
    }

    public function deactivate(): UserInterface
    {
        $this->active = self::INACTIVE_USER;
        return $this;
    }

    public function getSalt(): ?string
    {
        return null;
    }

    public function eraseCredentials(): ?string
    {
        return null;
    }
}
