<?php

namespace App\Application\Model;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Security\Core\User\UserInterface;

/**
 * @ORM\Entity(repositoryClass="App\Application\Repository\UserRepository")
 * @ORM\Table(name="user")
 */
class User implements UserInterface
{
    CONST ACTIVE_USER = 1;
    CONST INACTIVE_USER = 0;

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

    /** @ORM\OneToMany(targetEntity="Role", mappedBy="user") */
    private $roles;

    /** @ORM\Column(type="string") */
    private $token;

    /** @ORM\Column(type="string") */
    private $active;

    public function __construct(
        string $username,
        string $password,
        array $roles,
        string $token
    ) {
        $this->username = $username;
        $this->password = $password;
        $this->roles = new ArrayCollection($roles);
        $this->token = $token;

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

    public function setPassword(string $password): UserInterface
    {
        $this->password = $password;
        return $this;
    }

    public function getRoles(): Collection
    {
        return $this->roles;
    }

    public function addRole(Role $role): UserInterface
    {
        if (!$this->hasRole($role)) {
            $this->roles->add($role);
        }

        return $this;
    }

    public function removeRole(Role $role): UserInterface
    {
        if ($this->hasRole($role)) {
            $this->roles->removeElement($role);
        }

        return $this;
    }

    public function hasRole(Role $role): bool
    {
        return $this->roles->contains($role);
    }

    public function hasRoles(): bool
    {
        return !$this->roles->isEmpty();
    }

    public function getToken(): string
    {
        return $this->token;
    }

    public function setToken(string $token): UserInterface
    {
        $this->token = $token;
        return $this;
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

    public function getSalt()
    {
        return;
    }

    public function eraseCredentials()
    {
        return;
    }
}