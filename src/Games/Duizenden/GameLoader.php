<?php

declare(strict_types=1);

namespace App\Games\Duizenden;

use App\CardPool\CardPool;
use App\Cards\Standard\CardHelper;
use App\Cards\Standard\Exception\InvalidCardIdException;
use App\Common\Meld\Meld;
use App\Common\Meld\Melds;
use App\Deck\Card\CardInterface;
use App\DeckRebuilder\DeckRebuilderFactory;
use App\DeckRebuilders\DeckRebuilderType;
use App\Enum\Exception\EnumConstantsCouldNotBeResolvedException;
use App\Enum\Exception\EnumNotDefinedException;
use App\Games\Duizenden;
use App\Games\Duizenden\Entity\GamePlayer;
use App\Games\Duizenden\Entity\GamePlayerMeta;
use App\Games\Duizenden\Initializer\DiscardedCardPool;
use App\Games\Duizenden\Meld\TypeHelper;
use App\Games\Duizenden\Persistence\Exception\GameNotFoundException;
use App\Games\Duizenden\Player\Exception\PlayerNotFoundException;
use App\Games\Duizenden\Player\PlayerFactory;
use App\Games\Duizenden\Player\PlayerInterface;
use App\Games\Duizenden\Player\Players;
use App\Games\Duizenden\Repository\GameRepository;
use App\Shuffler\ShufflerFactory;
use App\Shufflers\Overhand\OverhandShuffle;
use App\Shufflers\ShufflerType;
use Doctrine\ORM\NonUniqueResultException;

class GameLoader
{
	private GameRepository $game_repository;

	private ShufflerFactory $shuffler_factory;

	private PlayerFactory $player_factory;

	private DeckRebuilderFactory $deck_rebuilder_factory;

	public function __construct(
		GameRepository $game_repository,
		ShufflerFactory $shuffler_factory,
		PlayerFactory $player_factory,
		DeckRebuilderFactory $deck_rebuilder_factory
	)
	{
		$this->game_repository = $game_repository;
		$this->shuffler_factory = $shuffler_factory;
		$this->player_factory = $player_factory;
		$this->deck_rebuilder_factory = $deck_rebuilder_factory;
	}

	/**
	 * @throws GameNotFoundException
	 * @throws InvalidCardIdException
	 * @throws NonUniqueResultException
	 * @throws PlayerNotFoundException
	 * @throws EnumConstantsCouldNotBeResolvedException
	 * @throws EnumNotDefinedException
	 */
	public function load(Duizenden\Game $game, string $uuid): void
	{
		$game_entity = $this->game_repository->loadLastGameState($uuid);

		if (null === $game_entity)
		{
			throw new GameNotFoundException(sprintf("Game with id '%s' was not found.", $uuid));
		}

		$game->setId($game_entity->getGameMeta()->getUuid());
		$game->setDeckRebuilder($this->deck_rebuilder_factory->create(DeckRebuilderType::createEnum($game_entity->getGameMeta()->getDeckRebuilder())));

		$dealing_player = null;
		$current_player = null;

		$players = $this->createPlayers(
			$game_entity->getGamePlayers(),
			$game_entity->getGameMeta()->getDealingPlayerMeta() ? $game_entity->getGameMeta()->getDealingPlayerMeta()->getId() : null,
			$game_entity->getCurrentPlayer() ? $game_entity->getCurrentPlayer()->getId() : null,
			$dealing_player,
			$current_player
		);

		$undrawn_pool = $this->createUndrawnPool($game_entity);
		$discarded_pool = $this->createDiscardedPool($game_entity);
		$discarded_pool->isFirstCard($game_entity->getIsFirstCard());

		$state = new State($players, $dealing_player, $undrawn_pool, $discarded_pool);
		$state->setTargetScore($game_entity->getGameMeta()->getTargetScore());
		$state->setFirstMeldMinimumPoints($game_entity->getGameMeta()->getFirstMeldMinimumPoints());
		$state->setRoundFinishExtraPoints($game_entity->getGameMeta()->getRoundFinishExtraPoints());
        $state->setRound($game_entity->getRound());
        $state->setTurn($game_entity->getTurn());
		$state->setAllowFirstTurnRoundEnd($game_entity->getGameMeta()->allowFirstTurnRoundEnd());

		if ($current_player)
		{
			$state->getPlayers()->setCurrentPlayer($current_player);
		}

		$game->setState($state);
	}

	/**
	 * @param GamePlayer[] $game_players

	 * @return Players
	 *
	 * @throws InvalidCardIdException
	 */
	private function createPlayers(
		iterable $game_players,
		?int $dealing_player_id,
		?int $current_player_id,
		PlayerInterface &$dealing_player = null,
		PlayerInterface &$current_player = null
	): Players
	{
		$players = new Players();

		foreach ($game_players as $game_player)
		{
			$player = $this->createPlayer($game_player);
			$players->addPlayer($player);

			if ($dealing_player_id && $dealing_player_id === $game_player->getGamePlayerMeta()->getId())
			{
				$dealing_player = $player;
			}

			if ($current_player_id && $current_player_id === $game_player->getId())
			{
				$current_player = $player;
			}
		}

		return $players;
	}

	/**
	 * @throws InvalidCardIdException
	 */
	private function createPlayer(GamePlayer $game_player): PlayerInterface
	{
		$meta = $game_player->getGamePlayerMeta();
		$entity = $meta->getPlayer();

		$shuffler = $this->createShuffler($meta);
		$melds = $this->createMelds($game_player->getMelds());
		$hand = $this->createHand($game_player);

		$player = $this->player_factory->create($entity->getUuid(), $hand, $melds, $shuffler);
		$player->setId($entity->getUuid());

		return $player;
	}

	/**
	 * @throws InvalidCardIdException
	 */
	private function createUndrawnPool(Entity\Game $game): CardPool
	{
		return new CardPool($this->createCardPool($game->getUndrawnPool()));
	}

	/**
	 * @throws InvalidCardIdException
	 */
	private function createDiscardedPool(Entity\Game $game)
	{
		return new DiscardedCardPool($this->createCardPool($game->getDiscardedPool()));
	}

	/**
	 * @throws InvalidCardIdException
	 */
	private function createHand(GamePlayer $player)
	{
		return new CardPool($this->createCardPool($player->getHand()));
	}

	private function createShuffler(GamePlayerMeta $player_meta): OverhandShuffle
	{
		/** @var OverhandShuffle $shuffler */
		$shuffler = $this->shuffler_factory->create(ShufflerType::OVERHAND());
		$shuffler
			->setShuffleCountMin($player_meta->getShuffleCountMin())
			->setShuffleCountMax($player_meta->getShuffleCountMax())
			->setGrabCardsMin($player_meta->getGrabCardsMin())
			->setGrabCardsMax($player_meta->getGrabCardsMax())
			->setMaxInserts($player_meta->getMaxInserts());

		return $shuffler;
	}

	/**
	 * @param string[] $card_ids
	 *
	 * @return CardInterface[]
	 *
	 * @throws InvalidCardIdException
	 */
	private function createCardPool(array $card_ids): array
	{
		$cards = [];

		foreach ($card_ids as $card_id)
		{
			$cards[] = CardHelper::createCardFromId($card_id);
		}

		return $cards;
	}

	/**
	 * @param string[] $meld_array
	 *
	 * @return Melds
	 *
	 * @throws InvalidCardIdException
	 */
	private function createMelds(array $meld_array): Melds
	{
		$melds = new Melds();

		foreach ($meld_array as $meld)
		{
			$cards = $this->createCardPool($meld);
			$melds->addMeld(new Meld(new CardPool($cards), TypeHelper::detectMeldType($cards)));
		}

		return $melds;
	}
}
