<?php

namespace App\Application\Model;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Application\Repository\UserRepository")
 * @ORM\Table(name="user")
 */
class User
{
    /**
     * This is only prototype of user needed to create other functionalities
     * @todo Create authentication functionality and extend User Entity
     */

    /**
     * @ORM\Id()
     * @ORM\GeneratedValue(strategy="AUTO")
     * @ORM\Column(type="integer")
     */
    private $id;

    public function getId(): int
    {
        return $this->id;
    }
}