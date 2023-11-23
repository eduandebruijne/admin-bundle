<?php

namespace EDB\AdminBundle\Entity;

use EDB\AdminBundle\Util\StringUtils;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\MappedSuperclass
 */
class AbstractUser extends BaseEntity implements UserInterface, PasswordAuthenticatedUserInterface
{
    /**
     * @ORM\Column
     * @var string
     */
    protected $username;

    /**
     * @ORM\Column(nullable=true)
     * @var ?string
     */
    protected $password;

    public $plainPassword;

    /**
     * @ORM\Column(nullable=true)
     * @var ?string
     */
    protected $salt;

    /**
     * @ORM\Column(type="json")
     * @var array
     */
    protected $roles = [];

    /**
     * @ORM\Column(nullable=true)
     * @var string
     */
    protected $resetPasswordToken;

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

    public function getResetPasswordToken(): ?string
    {
        return $this->resetPasswordToken;
    }

    public function setResetPasswordToken(?string $resetPasswordToken): void
    {
        $this->resetPasswordToken = $resetPasswordToken;
    }

    public function eraseCredentials()
    {
        // Not needed, only the hashed password will be saved
    }
}
