<?php

namespace App\Games\Duizenden;

use App\Games\Duizenden\Repository\GameRepository;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\NonUniqueResultException as NonUniqueResultExceptionAlias;
use Doctrine\ORM\ORMException;

class GameManipulator
{
	/**
	 * @var GameRepository
	 */
	private $game_repository;

	/**
	 * @var EntityManagerInterface
	 */
	private $entity_manager;

	/**
	 * @param GameRepository $game_repository
	 * @param EntityManagerInterface $entity_manager
	 */
	public function __construct(
		GameRepository $game_repository,
		EntityManagerInterface $entity_manager
	)
	{
		$this->game_repository = $game_repository;
		$this->entity_manager = $entity_manager;
	}

	/**
	 * @param string $uuid
	 *
	 * @throws NonUniqueResultExceptionAlias
	 * @throws ORMException
	 */
	public function undoLastAction(string $uuid): void
	{
		$game_meta = $this->game_repository->loadLastGameState($uuid);

		$this->entity_manager->remove($game_meta);
		$this->entity_manager->flush();
	}
}