<?php

namespace App\Games\Duizenden\Persistence;

use App\CardPool\CardPool;
use App\Common\Meld\Melds;
use App\Entity\Player;
use App\Games\Duizenden\Entity;
use App\Games\Duizenden;
use App\Games\Duizenden\Persistence\Exception\GameNotFoundException;
use App\Games\Duizenden\Player\PlayerInterface;
use App\Games\Duizenden\Player\Players;
use App\Games\Duizenden\Workflow\MarkingType;
use App\Games\Duizenden\Repository\GameRepository;
use App\Repository\PlayerRepository;
use App\Shufflers\Overhand\OverhandShuffle;
use DateTimeImmutable;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\NonUniqueResultException;
use Exception;

class GamePersistence
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
	 * @var PlayerRepository
	 */
	private $player_repository;

	/**
	 * @param EntityManagerInterface $entity_manager
	 * @param GameRepository $game_repository
	 * @param PlayerRepository $player_repository
	 */
	public function __construct(
		EntityManagerInterface $entity_manager,
		GameRepository $game_repository,
		PlayerRepository $player_repository
	)
	{
		$this->entity_manager = $entity_manager;
		$this->game_repository = $game_repository;
		$this->player_repository = $player_repository;
	}

	/**
	 * @param Duizenden\Game $game
	 *
	 * @return string
	 *
	 * @throws NonUniqueResultException
	 */
	public function getMarking(Duizenden\Game $game): string
	{
		$marking = $game->getId()
			? $this->game_repository->getLatestGameStateMarking($game->getId())
			: MarkingType::CREATE()->getValue();

		return $marking;
	}

	/**
	 * @param Duizenden\Game $game
	 * @param string $workflow_marking
	 * @param array $context
	 *
	 * @throws GameNotFoundException
	 * @throws NonUniqueResultException
	 * @throws Exception
	 */
	public function persist(Duizenden\Game $game, string $workflow_marking, array $context): void
	{
		switch ($workflow_marking)
		{
			case MarkingType::CREATE()->getValue():
				$this->createNewGame($game, $workflow_marking);
				break;

			case MarkingType::CONFIGURED()->getValue():
				$this->configureGame($game, $workflow_marking);
				break;

			case MarkingType::START_TURN()->getValue():
			case MarkingType::CARD_DRAWN()->getValue():
			case MarkingType::CARDS_MELTED()->getValue():
			case MarkingType::TURN_END()->getValue():
			case MarkingType::ROUND_END()->getValue():
			case MarkingType::GAME_END()->getValue():
				$this->updateState($game, $workflow_marking, $context);
				break;
		}

		$this->entity_manager->flush();
	}

	/**
	 * @param Duizenden\Game $game
	 * @param string $workflow_marking
	 *
	 * @return Entity\Game
	 *
	 * @throws \Exception
	 */
	private function createNewGame(Duizenden\Game $game, string $workflow_marking): Entity\Game
	{
		$game_meta_entity = new Entity\GameMeta();

		$game_entity = new Entity\Game();
		$game_entity
			->setGame($game_entity)
			->setGameMeta($game_meta_entity)
			->setSequence(1)
			->setWorkflowMarking($workflow_marking)
			->setCreatedAt(new DateTimeImmutable())
			->setUndrawnPool([])
			->setDiscardedPool([])
			->setIsFirstCard(true)
			;

		$this->entity_manager->persist($game_meta_entity);
		$this->entity_manager->persist($game_entity);

		$game->setId($game_meta_entity->getUuid());

		return $game_entity;
	}

	/**
	 * @param Duizenden\Game $game
	 * @param string $workflow_marking
	 *
	 * @throws GameNotFoundException
	 * @throws NonUniqueResultException
	 */
	private function configureGame(Duizenden\Game $game, string $workflow_marking)
	{
		$state = $game->getState();
		$game_entity = $this->getLastGameState($game->getId());
		$players = $this->loadPlayers($state->getPlayers());
		$game_meta = $game_entity->getGameMeta();

		$game_meta->setTargetScore($game->getState()->getTargetScore());
		$game_meta->setFirstMeldMinimumPoints($game->getState()->getFirstMeldMinimumPoints());
		$game_meta->setDeckRebuilder($game->getDeckRebuilder()->getName());

		($new_game_entity = clone $game_entity)
			->setWorkflowMarking($workflow_marking)
			->setSequence($new_game_entity->getSequence() + 1)
			->setUndrawnPool($this->serializeCardPool($state->getUndrawnPool()))
			->setRound(1);

		$dealing_game_player_meta = null;

		foreach ($state->getPlayers()->getFreshLoopIterator() as $player)
		{
			/** @var OverhandShuffle $shuffler */
			$shuffler = $player->getShuffler();

			$game_player_meta_entity = (new Entity\GamePlayerMeta())
				->setGameMeta($game_meta)
				->setPlayer($players[$player->getId()])
				->setShuffleCountMin($shuffler->getShuffleCountMin())
				->setShuffleCountMax($shuffler->getShuffleCountMax())
				->setGrabCardsMin($shuffler->getGrabCardsMin())
				->setGrabCardsMax($shuffler->getGrabCardsMax())
				->setMaxInserts($shuffler->getMaxInserts());

			$game_player_entity = (new Entity\GamePlayer())
				->setGame($new_game_entity)
				->setGamePlayerMeta($game_player_meta_entity)
				->setHand([])
				->setMelds([]);

			$new_game_entity->addGamePlayer($game_player_entity);

			if ($player->getId() === $state->getDealingPlayer()->getId())
			{
				$game_meta->setDealingPlayerMeta($game_player_meta_entity);
				$new_game_entity->setCurrentPlayer($game_player_entity);
			}

			$this->entity_manager->persist($new_game_entity);
			$this->entity_manager->persist($game_player_meta_entity);
			$this->entity_manager->persist($game_player_entity);
			$this->entity_manager->persist($game_meta);
		}
	}

	/**
	 * @param Duizenden\Game $game
	 * @param string $workflow_marking
	 * @param array $context
	 *
	 * @throws GameNotFoundException
	 * @throws NonUniqueResultException
	 */
	private function updateState(Duizenden\Game $game, string $workflow_marking, array $context)
	{
		$state = $game->getState();
		$game_entity = $this->getLastGameState($game->getId());

		($new_game_entity = clone $game_entity)
			->setSequence($new_game_entity->getSequence() + 1)
			->setWorkflowMarking($workflow_marking)
			->setCreatedAt(new DateTimeImmutable())
			->setUndrawnPool($this->serializeCardPool($state->getUndrawnPool()))
			->setDiscardedPool($this->serializeCardPool($state->getDiscardedPool()))
			->setIsFirstCard($state->getDiscardedPool()->isFirstCard());

		if ($context['up_round'] ?? false)
		{
			$new_game_entity->setRound($new_game_entity->getRound() + 1);
		}

		$this->entity_manager->persist($new_game_entity);

		foreach ($new_game_entity->getGamePlayers() as $game_player_entity)
		{
			$player = $state->getPlayers()->getPlayerById($game_player_entity->getGamePlayerMeta()->getPlayer()->getUuid());

			($new_game_player_entity = clone $game_player_entity)
				->setGame($new_game_entity)
				->setHand($this->serializeCardPool($player->getHand()))
				->setMelds($this->serializeMelds($player->getMelds()));

			if ($state->getPlayers()->getCurrentPlayer()->getId() === $new_game_player_entity->getGamePlayerMeta()->getPlayer()->getUuid())
			{
				$new_game_entity->setCurrentPlayer($new_game_player_entity);
			}

			$this->entity_manager->persist($new_game_player_entity);
		}
	}

	/**
	 * @param PlayerInterface $player
	 * @param Entity\GamePlayer[] $game_players
	 *
	 * @return Entity\GamePlayer|null
	 */
	private function getCurrentGamePlayer(PlayerInterface $player, iterable $game_players): ?Entity\GamePlayer
	{
		foreach ($game_players as $game_player)
		{
			if ($game_player->getGamePlayerMeta()->getPlayer()->getUuid() === $player->getId())
			{
				return $game_player;
			}
		}

		return null;
	}

	/**
	 * @param CardPool $pool
	 *
	 * @return string[]
	 */
	private function serializeCardPool(CardPool $pool): array
	{
		$return = [];
		$cards = $pool->getCards();

		foreach ($cards as $card)
		{
			$return[] = $card->getSuit()->getName() . $card->getRank()->getName();
		}

		return $return;
	}

	/**
	 * @param Melds $melds
	 *
	 * @return string[][]
	 */
	private function serializeMelds(Melds $melds): array
	{
		$return = [];

		foreach ($melds as $meld)
		{
			$return[] = $this->serializeCardPool($meld->getCards());
		}

		return $return;
	}

	/**
	 * @param string $uuid
	 *
	 * @return Entity\Game
	 *
	 * @throws GameNotFoundException
	 * @throws NonUniqueResultException
	 */
	private function getLastGameState(string $uuid): Entity\Game
	{
		if (!$game = $this->game_repository->loadLastGameState($uuid))
		{
			throw new GameNotFoundException(sprintf("Game with id '%s' was not found.", $uuid));
		}

		return $game;
	}

	/**
	 * @param Players $players
	 *
	 * @return Player[]
	 */
	private function loadPlayers(Players $players): array
	{
		$ids = [];

		foreach ($players as $player)
		{
			$ids[] = $player->getId();
		}

		return $this->player_repository->findIndexedByPlayedIds($ids);
	}
}