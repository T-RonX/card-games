<?php

declare(strict_types=1);

namespace App\Games\Duizenden\Dealer;

use App\Games\Duizenden\Game;
use App\Games\Duizenden\Player\Player;
use App\Games\Duizenden\Repository\GameRepository;
use Doctrine\ORM\NonUniqueResultException;
use Doctrine\ORM\NoResultException;

class DealerFinder
{
	private GameRepository $repository;

	public function __construct(GameRepository $repository)
	{
		$this->repository = $repository;
	}

	/**
	 * @throws NonUniqueResultException
	 * @throws NoResultException
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