<?php

namespace App\Application\Service;

use App\Application\Model\User;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use Symfony\Component\Security\Core\User\UserInterface;

class UserManager
{
    private $entityManager;

    private $passwordEncoder;

    private $tokenStorage;

    public function __construct(
        EntityManagerInterface $entityManager,
        UserPasswordEncoderInterface $passwordEncoder,
        TokenStorageInterface $tokenStorage
    ) {
        $this->entityManager = $entityManager;
        $this->passwordEncoder = $passwordEncoder;
        $this->tokenStorage = $tokenStorage;
    }

    public function create(?string $username, ?string $password): ?UserInterface
    {
        if ($this->arePostCredentialsEmpty($username, $password)) {
            return null;
        }

        $user = new User($username);
        $user->setPassword($this->passwordEncoder->encodePassword($user, $password));

        return $user;
    }

    public function save(UserInterface $user): void
    {
        $this->entityManager->persist($user);
        $this->entityManager->flush();
    }

    public function delete(UserInterface $user): void
    {
        $this->entityManager->remove($user);
        $this->entityManager->flush();
    }

    public function getCurrentUser(): ?UserInterface
    {
        $token = $this->tokenStorage->getToken();

        if (!$token instanceof TokenInterface) {
            return null;
        }

        return $token->getUser();
    }

    private function arePostCredentialsEmpty(?string $username, ?string $password): bool
    {
        return (empty($username) || empty($password));
    }
}