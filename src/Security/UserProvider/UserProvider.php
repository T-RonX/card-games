<?php

declare(strict_types=1);

namespace App\Security\UserProvider;

use App\Entity\User;
use App\User\User\UserRepository;
use Doctrine\ORM\NonUniqueResultException;
use Symfony\Component\Security\Core\Exception\UsernameNotFoundException;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Core\User\UserProviderInterface;

class UserProvider implements UserProviderInterface
{
    private UserRepository $user_repository;

    public function __construct(UserRepository $user_repository)
    {
        $this->user_repository = $user_repository;
    }

    /**
     * @throws NonUniqueResultException
     */
    public function loadUserByUsername(string $username): ?User
    {
        $user = $this->user_repository->loadUserByUsername($username);

        if (!$user)
        {
            $ex = new UsernameNotFoundException();
            $ex->setUsername($username);
            throw $ex;
        }

        $user->setRoles(['IS_AUTHENTICATED_FULLY']); // TODO: this should not be here.

        return $user;
    }

    /**
     * @throws NonUniqueResultException
     */
    public function refreshUser(UserInterface $user): User
    {
        return $this->loadUserByUsername($user->getUsername());
    }

    public function supportsClass(string $class): bool
    {
        return $class === User::class;
    }
}