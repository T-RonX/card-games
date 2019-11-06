<?php

namespace App\Games\Duizenden;

use App\CardPool\CardPool;
use App\Games\Duizenden\Initializer\DiscardedCardPool;
use App\Games\Duizenden\Player\Player;
use App\Games\Duizenden\Player\PlayerInterface;
use App\Games\Duizenden\Player\Players;

class State
{
	/**
	 * @var CardPool
	 */
	private $undrawn_pool;

	/**
	 * @var CardPool
	 */
	private $discarded_pool;

	/**
	 * @var Player[]
	 */
	private $players;

	/**
	 * @var PlayerInterface
	 */
	private $dealing_player;

	/**
	 * @var int
	 */
	private $target_score = 1000;

	/**
	 * @var int
	 */
	private $first_meld_minimum_points = 30;

	/**
	 * @var int
	 */
	private $round_finish_extra_points = 0;

	/**
	 * @param Players $players
	 * @param PlayerInterface $dealing_player
	 * @param CardPool $undrawn_pool
	 * @param DiscardedCardPool $discarded_pool
	 */
	public function __construct(
		Players $players,
		PlayerInterface $dealing_player,
		CardPool $undrawn_pool,
		DiscardedCardPool $discarded_pool
	)
	{
		$this->undrawn_pool = $undrawn_pool;
		$this->discarded_pool = $discarded_pool;
		$this->players = $players;
		$this->dealing_player = $dealing_player;
	}

	/**
	 * @return CardPool
	 */
	public function getUndrawnPool(): CardPool
	{
		return $this->undrawn_pool;
	}

	/**
	 * @param CardPool $card_pool
	 *
	 * @return void
	 */
	public function setUndrawnPool(CardPool $card_pool): void
	{
		$this->undrawn_pool = $card_pool;
	}

	/**
	 * @return DiscardedCardPool
	 */
	public function getDiscardedPool(): DiscardedCardPool
	{
		return $this->discarded_pool;
	}

	/**
	 * @return Players
	 */
	public function getPlayers(): Players
	{
		return $this->players;
	}

	/**
	 * @return PlayerInterface
	 */
	public function getDealingPlayer(): PlayerInterface
	{
		return $this->dealing_player;
	}

	/**
	 * @param PlayerInterface $player
	 */
	public function setDealingPlayer(PlayerInterface $player): void
	{
		$this->dealing_player = $player;
	}

	/**
	 * @param int $score
	 */
	public function setTargetScore(int $score): void
	{
		$this->target_score = $score;
	}

	/**
	 * @return int
	 */
	public function getTargetScore(): int
	{
		return $this->target_score;
	}

	/**
	 * @param int $points
	 */
	public function setFirstMeldMinimumPoints(int $points): void
	{
		$this->first_meld_minimum_points = $points;
	}

	/**
	 * @return int
	 */
	public function getFirstMeldMinimumPoints(): int
	{
		return $this->first_meld_minimum_points;
	}

	public function setRoundFinishExtraPoints(int $round_finish_extra_points): self
	{
		$this->round_finish_extra_points = $round_finish_extra_points;

		return $this;
	}

	public function getRoundFinishExtraPoints(): int
	{
		return $this->round_finish_extra_points;
	}
}