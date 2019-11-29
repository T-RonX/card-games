<?php

declare(strict_types=1);

namespace  App\Games\Duizenden\Actions\Meld;

use App\Deck\Card\CardInterface;
use App\Games\Duizenden\Player\Exception\PlayerNotFoundException;
use App\Games\Duizenden\Player\PlayerInterface;
use App\Games\Duizenden\Repository\GamePlayerRepository;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\NonUniqueResultException;
use Doctrine\ORM\ORMException;

class RevertMeld
{
	private EntityManagerInterface $entity_manager;

	private GamePlayerRepository $game_player_repository;

	public function __construct(
		EntityManagerInterface $entity_manager,
		GamePlayerRepository $game_player_repository
	)
	{
		$this->game_player_repository = $game_player_repository;
		$this->entity_manager = $entity_manager;
	}

	/**
	 * @throws NonUniqueResultException
	 * @throws PlayerNotFoundException
	 * @throws ORMException
	 */
	public function revert(string $game_id, PlayerInterface $player): void
	{
		$game_player = $this->game_player_repository->getLatestPlayer($game_id, $player->getId());

		if (!$game_player)
		{
			throw new PlayerNotFoundException(sprintf("Unable to revert melds, player with id '%s' was not found in game '%s'.",
					$player->getId(),
					$game_player)
			);
		}

		$cards = $player->getMelds()->drawAllCardsAndClearMelds();
		$player->getHand()->addCards($cards);

		$storable_cards = $this->createStorableCardArray($cards);

		$game_player->setHand([...$game_player->getHand(), ...$storable_cards]);
		$game_player->setMelds([]);

		$this->entity_manager->persist($game_player);
		$this->entity_manager->flush();
	}

	/**
	 * @param CardInterface[] $cards
	 *
	 * @return string[]
	 */
	private function createStorableCardArray(array $cards): array
	{
		$storable_cards = [];

		foreach ($cards as $card)
		{
			$storable_cards[] = $card->getIdentifier();
		}

		return $storable_cards;
	}
}