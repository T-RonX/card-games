<?php

namespace App\Games\Duizenden\Dealer;

use App\Games\Duizenden\Game;
use App\Games\Duizenden\Player\Player;
use App\Games\Duizenden\Repository\GameRepository;
use Doctrine\ORM\NonUniqueResultException;

class DealerFinder
{
	/**
	 * @var GameRepository
	 */
	private $repository;

	public function __construct(GameRepository $repository)
	{
		$this->repository = $repository;
	}

	/**
	 * @param Game $game
	 *
	 * @return Player
	 *
	 * @throws NonUniqueResultException
	 */
	public function findNextDealer(Game $game): ?Player
	{
		$first_dealer = $game->getState()->getDealingPlayer();
		$rounds_played = $this->repository->getRoundsPlayed($game->getId());
		$iterator = $game->getState()->getPlayers()->getInfiniteLoopIterator($first_dealer);

		for ($i = 0; $i < $rounds_played; ++$i)
		{
			$iterator->next();
		}

		return $iterator->current();
	}
}