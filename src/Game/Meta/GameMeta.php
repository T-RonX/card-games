<?php

namespace App\Game\Meta;

use DateTimeImmutable;

class GameMeta
{
	/**
	 * @var string
	 */
	private $game;

	/**
	 * @var string
	 */
	private $id;

	/**
	 * @var string
	 */
	private $place;

	/**
	 * @var DateTimeImmutable
	 */
	private $last_action_at;

	/**
	 * @param string $game
	 * @param string $id
	 * @param DateTimeImmutable $last_action_at
	 * @param string $place
	 */
	public function __construct(string $game, string $id, DateTimeImmutable $last_action_at, string $place)
	{
		$this->game = $game;
		$this->id = $id;
		$this->last_action_at = $last_action_at;
		$this->place = $place;
	}

	/**
	 * @return string
	 */
	public function getGame(): string
	{
		return $this->game;
	}

	/**
	 * @return string
	 */
	public function getId(): string
	{
		return $this->id;
	}

	/**
	 * @return DateTimeImmutable
	 */
	public function getLastActionAt(): DateTimeImmutable
	{
		return $this->last_action_at;
	}

	/**
	 * @return string
	 */
	public function getPlace(): string
	{
		return $this->place;
	}
}