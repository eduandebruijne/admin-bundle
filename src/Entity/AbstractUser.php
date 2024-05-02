<?php

namespace EDB\AdminBundle\Entity;

use Doctrine\ORM\Mapping\Column;
use Doctrine\ORM\Mapping\MappedSuperclass;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;
use Symfony\Component\Security\Core\User\UserInterface;

#[MappedSuperclass]
class AbstractUser extends BaseEntity implements UserInterface, PasswordAuthenticatedUserInterface
{
    #[Column(type: 'string')]
    protected string $username;

    #[Column(type: 'string', nullable: true)]
    protected $password;

    public $plainPassword;

    #[Column(type: 'string', nullable: true)]
    protected $salt;

    #[Column(type: 'json')]
    protected $roles = [];

    public function __toString()
    {
        return $this->getUserIdentifier();
    }

    public function getUserIdentifier(): string
    {
        return $this->getUsername();
    }

    public function getRoles(): array
    {
        return $this->roles;
    }

    public function setRoles(array $roles)
    {
        $this->roles = $roles;
    }

    public function getUsername(): string
    {
        return $this->username;
    }

    public function setUsername(string $username)
    {
        $this->username = $username;
    }

    public function getPassword(): ?string
    {
        return $this->password;
    }

    public function setPassword(?string $password): void
    {
        $this->password = $password;
    }

    public function getSalt(): ?string
    {
        return $this->salt;
    }

    public function setSalt(?string $salt): void
    {
        $this->salt = $salt;
    }

    public function eraseCredentials(): void
    {
        $this->password = null;
        $this->plainPassword = null;
    }
}
