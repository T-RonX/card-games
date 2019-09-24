<?php

namespace App\Games\Duizenden\Networking\Message;

use App\CardPool\CardPoolInterface;
use App\CardPool\Exception\EmptyCardPoolException;
use App\Games\Duizenden\Initializer\DiscardedCardPool;
use App\Games\Duizenden\Player\Player;
use App\Games\Duizenden\Player\PlayerInterface;
use Symfony\Component\Mercure\Update;

class GameEventMessage
{
	/**
	 * @var MessageBuilder
	 */
	private $builder;

	/**
	 * @var TopicType
	 */
	private $topic_type;

	/**
	 * @var StatusType
	 */
	private $status;

	/**
	 * @var string
	 */
	private $identifier;

	/**
	 * @var string[]
	 */
	private $log_messages = [];

	/**
	 * @var PlayerInterface
	 */
	private $source_player;

	/**
	 * @var ActionType
	 */
	private $source_action;

	/**
	 * @var PlayerInterface
	 */
	private $current_player;

	/**
	 * @var ActionType[]
	 */
	private $allowed_actions;

	/**
	 * @var CardPoolInterface
	 */
	private $undrawn_pool;

	/**
	 * @var DiscardedCardPool
	 */
	private $discarded_pool;

	/**
	 * @var Player[]
	 */
	private $players;

	/**
	 * @var int[]
	 */
	private $player_scores;

	/**
	 * @var string[]
	 */
	private $players_full_card_pool = [];

	/**
	 * @var string
	 */
	private $game_id;

	public function __construct(MessageBuilder $builder, TopicType $topic, string $identifier, StatusType $status)
	{
		$this->topic_type = $topic;
		$this->identifier = $identifier;
		$this->builder = $builder;
		$this->status = $status;
	}


	/**
	 * @param ActionType[] $actions
	 *
	 * @return GameEventMessage
	 */
	public function setAllowedActions(array $actions): self
	{
		$this->allowed_actions = $actions;

		return $this;
	}

	public function addAllowedAction(ActionType $action): void
	{
		$this->allowed_actions[] = $action;
	}

	/**
	 * @return ActionType[]
	 */
	public function getAllowedActions(): array
	{
		return $this->allowed_actions;
	}

	/**
	 * @return Update
	 *
	 * @throws EmptyCardPoolException
	 * @throws InvalidActionException
	 */
	public function create(): Update
	{
		return $this->builder->compile($this);
	}

	public function setStatus(StatusType $status): self
	{
		$this->status = $status;

		return $this;
	}

	public function getStatus(): StatusType
	{
		return $this->status;
	}

	public function addLogMessages(string $message): self
	{
		$this->log_messages[] = $message;

		return $this;
	}

	public function getLogMessages(): array
	{
		return $this->log_messages;
	}

	public function getTopicType(): TopicType
	{
		return $this->topic_type;
	}

	public function getIdentifier(): string
	{
		return $this->identifier;
	}

	public function setSourcePlayer(PlayerInterface $player): self
	{
		$this->source_player = $player;

		return $this;
	}

	public function getSourcePlayer(): PlayerInterface
	{
		return $this->source_player;
	}

	public function getCurrentPlayer(): PlayerInterface
	{
		return $this->current_player;
	}

	public function setCurrentPlayer(PlayerInterface $player): self
	{
		$this->current_player = $player;

		return $this;
	}

	public function getSourceAction(): ActionType
	{
		return $this->source_action;
	}

	public function setSourceAction(ActionType $action): self
	{
		$this->source_action = $action;

		return $this;
	}

	public function getUndrawnPool(): CardPoolInterface
	{
		return $this->undrawn_pool;
	}

	public function setUndrawnPool(CardPoolInterface $undrawn_pool): self
	{
		$this->undrawn_pool = $undrawn_pool;

		return $this;
	}

	public function getDiscardedPool(): DiscardedCardPool
	{
		return $this->discarded_pool;
	}

	public function setDiscardedPool(DiscardedCardPool $discarded_pool): self
	{
		$this->discarded_pool = $discarded_pool;

		return $this;
	}

	/**
	 * @return PlayerInterface[]|iterable
	 */
	public function getPlayers(): iterable
	{
		return $this->players;
	}

	public function setPlayers(iterable $players): self
	{
		$this->players = $players;

		return $this;
	}

	public function getPlayerScore(PlayerInterface $player): ?int
	{
		return $this->player_scores[$player->getId()] ?? null;
	}

	public function setPlayerScore(PlayerInterface $player, int $score): self
	{
		$this->player_scores[$player->getId()] = $score;

		return $this;
	}

	public function addPlayersFullCardPool(string $id): void
	{
		$this->players_full_card_pool[] = $id;
	}

	public function hasPlayerFullCardPool(string $id): bool
	{
		return in_array($id, $this->players_full_card_pool);
	}

	public function getGameId(): string
	{
		return $this->game_id;
	}

	public function setGameId(string $game_id): self
	{
		$this->game_id = $game_id;

		return $this;
	}
}