<?php

namespace App\Application\Model;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Security\Core\User\UserInterface;

/**
 * @ORM\Entity(repositoryClass="App\Application\Repository\RoleRepository")
 * @ORM\Table(name="role")
 */
class Role
{
    CONST ROLE_USER = 'user';
    CONST ROLE_ADMIN = 'admin';

    /**
     * @ORM\Id()
     * @ORM\GeneratedValue(strategy="AUTO")
     * @ORM\Column(type="integer")
     */
    private $id;

    /** @ORM\Column(type="string") */
    private $name;

    /** @ORM\Column(type="string") */
    private $description;

    /**
     * @ORM\ManyToOne(targetEntity="User", inversedBy="roles")
     * @ORM\JoinColumn(name="user_id", referencedColumnName="id")
     */
    private $user;

    public function getId(): int
    {
        return $this->id;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function setName(string $name): Role
    {
        $this->name = $name;
        return $this;
    }

    public function getDescription(): string
    {
        return $this->description;
    }

    public function setDescription($description): Role
    {
        $this->description = $description;
        return $this;
    }

    public function getUser(): UserInterface
    {
        return $this->user;
    }

    public function setUser(User $user): Role
    {
        $this->user = $user;
        $user->addRole($this);
        return $this;
    }
}