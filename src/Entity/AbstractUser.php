<?php

namespace EDB\AdminBundle\Entity;

use Symfony\Component\Security\Core\User\UserInterface;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\MappedSuperclass
 */
class AbstractUser extends BaseEntity implements UserInterface
{
    /**
     * @ORM\Column
     * @var ?string
     */
    protected $username;

    /**
     * @ORM\Column(type="json")
     * @var array
     */
    protected $roles = [];

    public function __toString()
    {
        return $this->getUserIdentifier();
    }

    public function getAvatarHash(): string
    {
        return md5($this->username);
    }

    public function getUserIdentifier()
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

    public function getUsername(): ?string
    {
        return $this->username;
    }

    public function setUsername(string $username)
    {
        $this->username = $username;
    }

    public function eraseCredentials()
    {
    }

    public function getPassword()
    {
    }

    public function getSalt()
    {
    }
}
