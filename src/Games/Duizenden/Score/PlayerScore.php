<?php

declare(strict_types=1);

namespace App\Games\Duizenden\Score;

class PlayerScore
{
	private int $meld_points;

	private int $hand_points;

	private string $player_id;

	private bool $is_round_finisher;

	private int $round_finish_extra_points;

	public function __construct(string $player_id, int $meld_points, int $hand_points, bool $is_round_finisher, int $round_finish_extra_points)
	{
		$this->player_id = $player_id;
		$this->meld_points = $meld_points;
		$this->hand_points = $hand_points;
		$this->is_round_finisher = $is_round_finisher;
		$this->round_finish_extra_points = $round_finish_extra_points;
	}

	public function getPlayerId(): string
	{
		return $this->player_id;
	}

	public function getScore(): int
	{
		return ($this->meld_points - $this->hand_points) + $this->round_finish_extra_points;
	}

	public function getMeldPoints(): int
	{
		return $this->meld_points;
	}

	public function getHandPoints(): int
	{
		return $this->hand_points;
	}

	public function isRoundFinisher(): bool
	{
		return $this->is_round_finisher;
	}

	public function getRoundFinishExtraPoints(): int
	{
		return $this->round_finish_extra_points;
	}
}