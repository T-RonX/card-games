<?php

declare(strict_types=1);

namespace App\Security\UserProvider;

use App\Entity\Player;
use App\Repository\PlayerRepository;
use App\User\Player\PlayerFactory;
use Doctrine\ORM\NonUniqueResultException;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Core\User\UserProviderInterface;

class AnonymousPlayerProvider implements UserProviderInterface
{
    private const SESSION_ANON_PLAYER_KEY = 'player_id';
    private PlayerRepository $repository;
    private SessionInterface $session;
    private PlayerFactory $player_factory;

    public function __construct(
        PlayerRepository $repository,
        SessionInterface $session,
        PlayerFactory $player_factory
    )
    {
        $this->repository = $repository;
        $this->session = $session;
        $this->player_factory = $player_factory;
    }

    private function registerPlayer(string $name): Player
    {
        $player = $this->player_factory->create($name);
        $this->registerInSession($player);

        return $player;
    }

    /**
     * @throws NonUniqueResultException
     */
    private function getPlayer(): ?Player
    {
        $id = $this->session->get(self::SESSION_ANON_PLAYER_KEY);

        return $id ? $this->repository->findAnonymousPlayer($id) : null;
    }

    private function registerInSession(Player $player): void
    {
        $this->session->set(self::SESSION_ANON_PLAYER_KEY, $player->getUuid());
    }


    /**
     * @throws NonUniqueResultException
     */
    public function getOrCreatePlayer(string $username): Player
    {
        $player = $this->getPlayer();

        if (!$player)
        {
            $player = $this->registerPlayer($username);
        }

        return $player;
    }

    /**
     * @throws NonUniqueResultException
     */
    public function loadUserByUsername(string $username): UserInterface
    {
        return $this->getOrCreatePlayer($username);
    }

    /**
     * @throws NonUniqueResultException
     */
    public function refreshUser(UserInterface $user): UserInterface
    {
        return $this->getOrCreatePlayer($user->getUsername());
    }

    public function supportsClass($class): bool
    {
        return Player::class === $class;
    }
}