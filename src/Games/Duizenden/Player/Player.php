<?php

declare(strict_types=1);

namespace App\Games\Duizenden\Player;

use App\CardPool\CardPool;
use App\Common\Meld\Melds;
use App\Shuffler\ShufflerInterface;

class Player implements PlayerInterface
{
	private string $id;

	private string $name;

	private ShufflerInterface $shuffler;

	private CardPool $hand;

	private Melds $melds;

	public function setId(string $id): PlayerInterface
	{
		$this->id = $id;

		return $this;
	}

	public function getId(): string
	{
		return $this->id;
	}

	public function setName(string $name): PlayerInterface
	{
		$this->name = $name;

		return $this;
	}

	public function getName(): string
	{
		return $this->name;
	}

	public function setShuffler(ShufflerInterface $shuffler): PlayerInterface
	{
		$this->shuffler = $shuffler;

		return $this;
	}

	public function getShuffler(): ShufflerInterface
	{
		return $this->shuffler;
	}

	public function setHand(CardPool $hand): PlayerInterface
	{
		$this->hand = $hand;

		return $this;
	}

	public function getHand(): CardPool
	{
		return $this->hand;
	}

	public function setMelds(Melds $melds): PlayerInterface
	{
		$this->melds = $melds;

		return $this;
	}

	public function getMelds(): Melds
	{
		return $this->melds;
	}

	public function hasMelds(): bool
	{
		return count($this->melds) > 0;
	}

	public function equals(PlayerInterface $player): bool
	{
		return $player instanceof Player && $player->getId() == $this->id;
	}
}