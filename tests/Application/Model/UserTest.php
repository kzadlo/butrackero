<?php

namespace App\Tests\Application\Model;

use App\Application\Model\User;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Security\Core\User\UserInterface;

final class UserTest extends TestCase
{
    /** @var User $user */
    private $user;

    public function setUp(): void
    {
        $this->user = (new User('Tester'))
            ->changePassword('TesterPassword');
    }

    public function testClassImplementsUserInterface()
    {
        $this->assertInstanceOf(UserInterface::class, $this->user);
    }

    public function testUsernameSetsInConstructor()
    {
        $this->assertSame('Tester', $this->user->getUsername());
    }

    public function testUserIsActiveAfterCreation()
    {
        $this->assertTrue($this->user->isActive());
    }

    public function testCanGetUsername()
    {
        $this->assertSame('Tester', $this->user->getUsername());
    }

    public function testCanChangeUsername()
    {
        $this->user->changeUsername('Other');

        $this->assertSame('Other', $this->user->getUsername());
    }

    public function testCanGetPassword()
    {
        $this->assertSame('TesterPassword', $this->user->getPassword());
    }

    public function testCanChangePassword()
    {
        $this->user->changePassword('TestPass123');

        $this->assertSame('TestPass123', $this->user->getPassword());
    }

    public function testCanGetRoles()
    {
        $this->assertContains('ROLE_USER', $this->user->getRoles());
    }

    public function testCanActivateUser()
    {
        $this->user->activate();

        $this->assertTrue($this->user->isActive());
    }

    public function testCanDeactivateUser()
    {
        $this->user->deactivate();

        $this->assertFalse($this->user->isActive());
    }

    public function testIsUserActive()
    {
        $this->assertTrue($this->user->isActive());

        $this->user->deactivate();

        $this->assertFalse($this->user->isActive());
    }

    public function testCanGetSalt()
    {
        $this->assertNull($this->user->getSalt());
    }

    public function canEraseCredentials()
    {
        $this->assertNull($this->user->eraseCredentials());
    }
}
