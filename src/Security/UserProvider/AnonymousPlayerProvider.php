<?php

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

	/**
	 * @var PlayerRepository
	 */
	private $repository;

	/**
	 * @var EntityManagerInterface
	 */
	private $entity_manager;

	/**
	 * @var SessionInterface
	 */
	private $session;

	/**
	 * @param EntityManagerInterface $entity_manager
	 * @param PlayerRepository $repository
	 * @param SessionInterface $session
	 */
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

	/**
	 * @param string $name
	 *
	 * @return Player
	 */
	private function registerPlayer(string $name): Player
	{
		$player = $this->createPlayerEntity($name);
		$this->savePlayer($player);
		$this->registerInSession($player);

		return $player;
	}

	/**
	 * @return Player|null
	 *
	 * @throws NonUniqueResultException
	 */
	private function getPlayer(): ?Player
	{
		$id = $this->session->get(self::SESSION_ANON_PLAYER_KEY);

		return $id ? $this->repository->findAnonymousPlayer($id) : null;
	}

	/**
	 * @param Player $player
	 */
	private function registerInSession(Player $player): void
	{
		$this->session->set(self::SESSION_ANON_PLAYER_KEY, $player->getUuid());
	}

	/**
	 * @param string $name
	 *
	 * @return Player
	 */
	private function createPlayerEntity(string $name): Player
	{
		return (new Player())
			->setName($name)
			->setIsRegistered(false);
	}

	/**
	 * @param Player $player
	 */
	private function savePlayer(Player $player): void
	{
		$this->entity_manager->persist($player);
		$this->entity_manager->flush();
	}

	/**
	 * @throws NonUniqueResultException
	 */
	public function getOrCreatePlayer(string $username)
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
	public function loadUserByUsername($username)
	{
		return $this->getOrCreatePlayer($username);
	}

	/**
	 * @throws NonUniqueResultException
	 */
	public function refreshUser(UserInterface $user)
	{
		return $this->getOrCreatePlayer($user->getUsername());
	}

	public function supportsClass($class)
	{
		return Player::class === $class;
	}
}