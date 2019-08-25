<?php

namespace App\Games\Duizenden;

use App\DeckRebuilder\DeckRebuilderFactory;
use App\DeckRebuilders\DeckRebuilderType;
use App\Enum\Exception\EnumConstantsCouldNotBeResolvedException;
use App\Enum\Exception\EnumNotDefinedException;
use App\Games\Duizenden\Persistence\Exception\GameNotFoundException;
use App\CardPool\CardPool;
use App\Cards\Standard\CardHelper;
use App\Cards\Standard\Exception\InvalidCardIdException;
use App\Common\Meld\Meld;
use App\Common\Meld\Melds;
use App\Deck\Card\CardInterface;
use App\Games\Duizenden\Entity\GamePlayer;
use App\Games\Duizenden\Entity\GamePlayerMeta;
use App\Games\Duizenden;
use App\Games\Duizenden\Initializer\DiscardedCardPool;
use App\Games\Duizenden\Meld\TypeHelper;
use App\Games\Duizenden\Player\Exception\PlayerNotFoundException;
use App\Games\Duizenden\Player\PlayerFactory;
use App\Games\Duizenden\Player\PlayerInterface;
use App\Games\Duizenden\Player\Players;
use App\Games\Duizenden\Repository\GameRepository;
use App\Shuffler\ShufflerFactory;
use App\Shufflers\Overhand\OverhandShuffle;
use App\Shufflers\ShufflerType;
use Doctrine\ORM\NonUniqueResultException;
use App\Games\Duizenden\Workflow\MarkingType;

class GameLoader
{
	/**
	 * @var GameRepository
	 */
	private $game_repository;

	/**
	 * @var ShufflerFactory
	 */
	private $shuffler_factory;

	/**
	 * @var PlayerFactory
	 */
	private $player_factory;

	/**
	 * @var DeckRebuilderFactory
	 */
	private $deck_rebuilder_factory;

	/**
	 * @param GameRepository $game_repository
	 * @param ShufflerFactory $shuffler_factory
	 * @param PlayerFactory $player_factory
	 * @param DeckRebuilderFactory $deck_rebuilder_factory
	 */
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
	 * @param Duizenden\Game $game
	 * @param string $uuid
	 *
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

		if ($current_player)
		{
			$state->getPlayers()->setCurrentPlayer($current_player);
		}

		$game->setState($state);
	}

	/**
	 * @param GamePlayer[] $game_players
	 * @param int $dealing_player_id
	 * @param int $current_player_id
	 * @param PlayerInterface $dealing_player
	 * @param PlayerInterface $current_player
	 *
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
	 * @param GamePlayer $game_player
	 *
	 * @return PlayerInterface
	 *
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
	 * @param Entity\Game $game
	 *
	 * @return CardPool
	 *
	 * @throws InvalidCardIdException
	 */
	private function createUndrawnPool(Entity\Game $game): CardPool
	{
		return new CardPool($this->createCardPool($game->getUndrawnPool()));
	}

	/**
	 * @param Entity\Game $game
	 *
	 * @return DiscardedCardPool
	 *
	 * @throws InvalidCardIdException
	 */
	private function createDiscardedPool(Entity\Game $game)
	{
		return new DiscardedCardPool($this->createCardPool($game->getDiscardedPool()));
	}

	/**
	 * @param GamePlayer $player
	 *
	 * @return CardPool
	 *
	 * @throws InvalidCardIdException
	 */
	private function createHand(GamePlayer $player)
	{
		return new CardPool($this->createCardPool($player->getHand()));
	}

	/**
	 * @param GamePlayerMeta $player_meta
	 *
	 * @return OverhandShuffle
	 */
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
