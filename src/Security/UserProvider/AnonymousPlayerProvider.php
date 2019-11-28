<?php

declare(strict_types=1);

namespace App\Security\UserProvider;

use App\Entity\Player;
use App\Repository\PlayerRepository;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\NonUniqueResultException;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Core\User\UserProviderInterface;

class AnonymousPlayerProvider implements UserProviderInterface
{
	private const SESSION_ANON_PLAYER_KEY = 'player_id';

	private PlayerRepository $repository;

	private EntityManagerInterface $entity_manager;

	private SessionInterface $session;

	public function __construct(
		EntityManagerInterface $entity_manager,
		PlayerRepository $repository,
		SessionInterface $session
	)
	{
		$this->repository = $repository;
		$this->entity_manager = $entity_manager;
		$this->session = $session;
	}

	private function registerPlayer(string $name): Player
	{
		$player = $this->createPlayerEntity($name);
		$this->savePlayer($player);
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

	private function createPlayerEntity(string $name): Player
	{
		return (new Player())
			->setName($name)
			->setIsRegistered(false);
	}

	private function savePlayer(Player $player): void
	{
		$this->entity_manager->persist($player);
		$this->entity_manager->flush();
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