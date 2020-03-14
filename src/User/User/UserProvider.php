<?php

declare(strict_types=1);

namespace App\User\User;

use App\Entity\Player;
use App\Entity\User;
use App\Security\Authentication\Token\AnonymousPlayerToken;
use Symfony\Component\Security\Core\Authentication\Token\AnonymousToken;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;

class UserProvider
{
    private TokenStorageInterface $token_storage;

    public function __construct(TokenStorageInterface $token_storage)
    {
        $this->token_storage = $token_storage;
    }

    public function isRegistered(): bool
    {
        return null !== $this->getPlayer() && null !== $this->getPlayer()->getUser();
    }

    public function getUser(): ?User
    {
        return $this->getPlayer()->getUser();
    }

    public function getPlayer(): ?Player
    {
        $user = $this->getToken()->getUser();

        if ($user instanceof Player)
        {
            return $user;
        }
        elseif ($user instanceof User)
        {
            return $user->getPlayer();
        }

        return null;
    }

    public function isAuthenticated(): bool
    {
        return null !== $this->getToken() && !$this->getToken() instanceof AnonymousToken;
    }

    public function isAnonymous(): bool
    {
        return $this->getToken() instanceof AnonymousPlayerToken;
    }

    private function getToken(): ?TokenInterface
    {
        return $this->token_storage->getToken();
    }
}