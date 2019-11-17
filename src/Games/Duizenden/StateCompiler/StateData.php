<?php

namespace App\Games\Duizenden\StateCompiler;

use App\CardPool\CardPoolInterface;
use App\Games\Duizenden\Initializer\DiscardedCardPool;
use App\Games\Duizenden\Player\Player;
use App\Games\Duizenden\Player\PlayerInterface;

class StateData
{
	/**
	 * @var StateCompilerInterface
	 */
	private $compiler;


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

	/**
	 * @var int
	 */
	private $target_score;

	/**
	 * @var int
	 */
	private $first_meld_minimum_points;

	/**
	 * @var int
	 */
	private $round_finish_extra_points;

	/**
	 * @var array|null
	 */
	private $extra;

	public function __construct(StateCompilerInterface $compiler)
	{
		$this->compiler = $compiler;
	}

	/**
	 * @param ActionType[] $actions
	 *
	 * @return self
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
	 * @return array
	 */
	public function create(): array
	{
		return $this->compiler->compile($this);
	}

	public function setSourcePlayer(PlayerInterface $player): self
	{
		$this->source_player = $player;

		return $this;
	}

	public function hasSource(): bool
	{
		return null !== $this->source_player && null !== $this->source_action;
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

	public function setExtras(?array $extra): self
	{
		$this->extra = $extra;

		return $this;
	}

	public function addExtra(string $key, $value): self
	{
		$this->extra[$key] = $value;

		return $this;
	}

	public function getExtras(): ?array
	{
		return $this->extra;
	}

	/**
	 * @return int
	 */
	public function getTargetScore(): int
	{
		return $this->target_score;
	}

	/**
	 * @param int $target_score
	 *
	 * @return self
	 */
	public function setTargetScore(int $target_score): self
	{
		$this->target_score = $target_score;

		return $this;
	}

	/**
	 * @return int
	 */
	public function getFirstMeldMinimumPoints(): int
	{
		return $this->first_meld_minimum_points;
	}

	/**
	 * @param int $first_meld_minimum_points
	 *
	 * @return StateData
	 */
	public function setFirstMeldMinimumPoints(int $first_meld_minimum_points): self
	{
		$this->first_meld_minimum_points = $first_meld_minimum_points;

		return $this;
	}

	/**
	 * @return int
	 */
	public function getRoundFinishExtraPoints(): int
	{
		return $this->round_finish_extra_points;
	}

	public function setRoundFinishExtraPoints(int $round_finish_extra_points): self
	{
		$this->round_finish_extra_points = $round_finish_extra_points;

		return $this;
	}
}