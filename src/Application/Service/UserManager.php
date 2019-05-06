<?php

namespace App\Application\Service;

use App\Application\Factory\UserFactory;
use App\Application\Repository\UserRepository;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\User\UserInterface;

class UserManager
{
    private $userFactory;

    private $userRepository;

    private $tokenStorage;

    public function __construct(
        UserFactory $userFactory,
        UserRepository $userRepository,
        TokenStorageInterface $tokenStorage
    ) {
        $this->userFactory = $userFactory;
        $this->userRepository = $userRepository;
        $this->tokenStorage = $tokenStorage;
    }

    public function add(?string $username, ?string $plainPassword): bool
    {
        if (!$this->canCreate($username, $plainPassword)) {
            return false;
        }

        $user = $this->userFactory->create($username, $plainPassword);
        $this->userRepository->save($user);

        return true;
    }

    public function getCurrentUser(): ?UserInterface
    {
        $token = $this->tokenStorage->getToken();

        if (!$token instanceof TokenInterface) {
            return null;
        }

        return $token->getUser();
    }

    private function canCreate(?string $username, ?string $password): bool
    {
        return !(empty($username) || empty($password));
    }
}
