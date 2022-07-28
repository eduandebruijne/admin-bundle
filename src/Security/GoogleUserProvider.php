<?php

namespace EDB\AdminBundle\Security;

use League\OAuth2\Client\Provider\GoogleUser;
use Symfony\Component\Security\Core\Exception\UnsupportedUserException;
use Symfony\Component\Security\Core\Exception\UsernameNotFoundException;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Core\User\UserProviderInterface;
use Doctrine\ORM\EntityManagerInterface;
use Exception;

class GoogleUserProvider implements UserProviderInterface
{
    private EntityManagerInterface $em;

    private string $userClass;

    public function __construct(EntityManagerInterface $em, ?string $userClass)
    {
        if (!$userClass) {
            throw new Exception('No user class is implemented in this project.');
        }

        $this->em = $em;
        $this->userClass = $userClass;
    }

    /**
     * Loads the user for the given username.
     *
     * This method must throw UsernameNotFoundException if the user is not
     * found.
     *
     * @param string $username The username
     *
     * @return UserInterface
     *
     * @throws UsernameNotFoundException if the user is not found
     */
    public function loadUserByUsername($username)
    {
        return $this->em->getRepository($this->userClass)->findOneBy([
            'username' => $username
        ]);
    }

    public function loadUserByIdentifier(string $identifier): UserInterface
    {
        return $this->loadUserByUsername($identifier);
    }

    /**
     * Refreshes the user.
     *
     * It is up to the implementation to decide if the user data should be
     * totally reloaded (e.g. from the database), or if the UserInterface
     * object can just be merged into some internal array of users / identity
     * map.
     *
     * @return UserInterface
     *
     * @throws UnsupportedUserException  if the user is not supported
     * @throws UsernameNotFoundException if the user is not found
     */
    public function refreshUser(UserInterface $user)
    {
        return $this->loadUserByUsername($user->getUserIdentifier());
    }

    /**
     * Whether this provider supports the given user class.
     *
     * @param string $class
     *
     * @return bool
     */
    public function supportsClass($class)
    {
        return GoogleUser::class === $class;
    }
}
