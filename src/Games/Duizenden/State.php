<?php

declare(strict_types=1);

namespace App\Games\Duizenden;

use App\CardPool\CardPool;
use App\Games\Duizenden\Initializer\DiscardedCardPool;
use App\Games\Duizenden\Player\PlayerInterface;
use App\Games\Duizenden\Player\Players;

class State
{
	private CardPool $undrawn_pool;

	private CardPool $discarded_pool;

	private Players $players;

	private PlayerInterface$dealing_player;

	private int $target_score = 1000;

	private int $first_meld_minimum_points = 30;

	private int $round_finish_extra_points = 0;

	public function __construct(
		 $players,
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

	public function getUndrawnPool(): CardPool
	{
		return $this->undrawn_pool;
	}

	public function setUndrawnPool(CardPool $card_pool): void
	{
		$this->undrawn_pool = $card_pool;
	}

	public function getDiscardedPool(): DiscardedCardPool
	{
		return $this->discarded_pool;
	}

	public function getPlayers(): Players
	{
		return $this->players;
	}

	public function getDealingPlayer(): PlayerInterface
	{
		return $this->dealing_player;
	}

	public function setDealingPlayer(PlayerInterface $player): void
	{
		$this->dealing_player = $player;
	}

	public function setTargetScore(int $score): void
	{
		$this->target_score = $score;
	}

	public function getTargetScore(): int
	{
		return $this->target_score;
	}

	public function setFirstMeldMinimumPoints(int $points): void
	{
		$this->first_meld_minimum_points = $points;
	}

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