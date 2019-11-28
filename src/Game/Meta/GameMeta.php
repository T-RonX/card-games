<?php

declare(strict_types=1);

namespace App\Game\Meta;

use DateTimeImmutable;

class GameMeta
{
	private string $id;

	private string $game;

	private string $place;

	private DateTimeImmutable $last_action_at;

	public function __construct(
		string $game,
		string $id,
		DateTimeImmutable $last_action_at,
		string $place
	)
	{
		$this->game = $game;
		$this->id = $id;
		$this->last_action_at = $last_action_at;
		$this->place = $place;
	}

	public function getGame(): string
	{
		return $this->game;
	}

	public function getId(): string
	{
		return $this->id;
	}

	public function getLastActionAt(): DateTimeImmutable
	{
		return $this->last_action_at;
	}

	public function getPlace(): string
	{
		return $this->place;
	}
}