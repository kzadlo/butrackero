<?php

namespace App\Application\Repository;

use App\Application\Model\User;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Symfony\Bridge\Doctrine\RegistryInterface;
use Symfony\Component\Security\Core\User\UserInterface;

final class UserRepository extends ServiceEntityRepository
{
    public function __construct(RegistryInterface $registry)
    {
        parent::__construct($registry, User::class);
    }

    public function save(UserInterface $user): void
    {
        $this->_em->persist($user);
        $this->_em->flush();
    }

    public function delete(UserInterface $user): void
    {
        $this->_em->remove($user);
        $this->_em->flush();
    }
}
