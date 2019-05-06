<?php

namespace App\Application\Factory;

use App\Application\Model\User;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use Symfony\Component\Security\Core\User\UserInterface;

class UserFactory
{
    private $passwordEncoder;

    public function __construct(UserPasswordEncoderInterface $passwordEncoder)
    {
        $this->passwordEncoder = $passwordEncoder;
    }

    public function create(string $username, string $plainPassword): ?UserInterface
    {
        $user = new User($username);
        $user->changePassword($this->passwordEncoder->encodePassword($user, $plainPassword));

        return $user;
    }
}
