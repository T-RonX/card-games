<?php

namespace App\Games\Duizenden\Score;

class PlayerScore
{
	/**
	 * @var int
	 */
	private $meld_points;

	/**
	 * @var int
	 */
	private $hand_points;

	/**
	 * @var string
	 */
	private $player_id;

	/**
	 * @var bool
	 */
	private $is_round_finisher;

	/**
	 * @var int
	 */
	private $round_finish_extra_points;

	/**
	 * @param string $player_id
	 * @param int $meld_points
	 * @param int $hand_points
	 * @param int $round_finish_extra_points
	 * @param bool $is_round_finisher
	 */
	public function __construct(string $player_id, int $meld_points, int $hand_points, bool $is_round_finisher, int $round_finish_extra_points)
	{
		$this->player_id = $player_id;
		$this->meld_points = $meld_points;
		$this->hand_points = $hand_points;
		$this->is_round_finisher = $is_round_finisher;
		$this->round_finish_extra_points = $round_finish_extra_points;
	}

	/**
	 * @return string
	 */
	public function getPlayerId(): string
	{
		return $this->player_id;
	}

	/**
	 * @return int
	 */
	public function getScore(): int
	{
		return ($this->meld_points - $this->hand_points) + $this->round_finish_extra_points;
	}

	/**
	 * @return int
	 */
	public function getMeldPoints(): int
	{
		return $this->meld_points;
	}

	/**
	 * @return int
	 */
	public function getHandPoints(): int
	{
		return $this->hand_points;
	}

	/**
	 * @return bool
	 */
	public function isRoundFinisher(): bool
	{
		return $this->is_round_finisher;
	}

	/**
	 * @return int
	 */
	public function getRoundFinishExtraPoints(): int
	{
		return $this->round_finish_extra_points;
	}
}