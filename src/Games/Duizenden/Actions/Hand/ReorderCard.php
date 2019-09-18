<?php

namespace  App\Games\Duizenden\Actions\Hand;

use App\Games\Duizenden\Actions\StateChangeAction;
use App\Games\Duizenden\Game;
use App\Games\Duizenden\Player\Exception\PlayerNotFoundException;
use App\Games\Duizenden\Player\PlayerInterface;
use App\Games\Duizenden\Repository\GamePlayerRepository;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\NonUniqueResultException;
use Doctrine\ORM\OptimisticLockException;
use Doctrine\ORM\ORMException;
use RuntimeException;
use Symfony\Component\Workflow\StateMachine;

class ReorderCard
{
	/**
	 * @var EntityManager
	 */
	private $entity_manager;

	/**
	 * @var GamePlayerRepository
	 */
	private $game_player_repository;

	/**
	 * @param EntityManagerInterface $entity_manager
	 * @param GamePlayerRepository $game_player_repository
	 */
	public function __construct(
		EntityManagerInterface $entity_manager,
		GamePlayerRepository $game_player_repository
	)
	{
		$this->game_player_repository = $game_player_repository;
		$this->entity_manager = $entity_manager;
	}

	/**
	 * @param Game $game
	 * @param PlayerInterface $player
	 * @param int $source
	 * @param int $target
	 *
	 * @throws NonUniqueResultException
	 * @throws ORMException
	 * @throws OptimisticLockException
	 * @throws PlayerNotFoundException
	 */
	public function reorder(Game $game, PlayerInterface $player, int $source, int $target): void
	{
		if ($source === $target)
		{
			return;
		}

		$game_player = $this->game_player_repository->getLatestPlayer($game->getId(), $player->getId());

		if (!$game_player)
		{
			throw new PlayerNotFoundException(sprintf("Unable to reorder hand, player with id '%s' was not found in game '%s'.",
					$player->getId(),
					$game_player)
			);
		}

		$hand = $game_player->getHand();

		if (!array_key_exists($source, $hand) || ($target < -1 && !array_key_exists($target, $hand)))
		{
			throw new RuntimeException(sprintf("Unable to reorder hand, cards index out of bound."));
		}

		if ($target === -1)
		{
			array_unshift($hand, array_splice($hand, $source, 1)[0]);
		}
		else
		{
			$moving_card = array_splice($hand, $source, 1);
			array_splice($hand, $source > $target ? $target + 1: $target, 0, $moving_card);
		}

		$game_player->setHand(array_values($hand));

		$this->entity_manager->persist($game_player);
		$this->entity_manager->flush();
	}
}