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
	 * @param string $player_id
	 * @param int $meld_points
	 * @param int $hand_points
	 */
	public function __construct(string $player_id, int $meld_points, int $hand_points)
	{
		$this->player_id = $player_id;
		$this->meld_points = $meld_points;
		$this->hand_points = $hand_points;
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
		return $this->meld_points - $this->hand_points;
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
}