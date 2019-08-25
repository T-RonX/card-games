<?php

namespace App\Games\Duizenden\Player;

use App\CardPool\CardPool;
use App\Common\Meld\Melds;
use App\Shuffler\ShufflerInterface;

class Player implements PlayerInterface
{
	/**
	 * @var int
	 */
	private $id;

	/**
	 * @var string
	 */
	private $name;

	/**
	 * @var ShufflerInterface
	 */
	private $shuffler;

	/**
	 * @var CardPool
	 */
	private $hand;

	/**
	 * @var Melds
	 */
	private $melds;

	/**
	 * @param string $id
	 *
	 * @return PlayerInterface
	 */
	public function setId(string $id): PlayerInterface
	{
		$this->id = $id;

		return $this;
	}

	/**
	 * @return string
	 */
	public function getId(): string
	{
		return $this->id;
	}

	/**
	 * @inheritDoc
	 */
	public function setName(string $name): PlayerInterface
	{
		$this->name = $name;

		return $this;
	}

	/**
	 * @return string
	 */
	public function getName(): string
	{
		return $this->name;
	}

	/**
	 * @inheritDoc
	 */
	public function setShuffler(ShufflerInterface $shuffler): PlayerInterface
	{
		$this->shuffler = $shuffler;

		return $this;
	}

	/**
	 * @inheritDoc
	 */
	public function getShuffler(): ShufflerInterface
	{
		return $this->shuffler;
	}

	/**
	 * @inheritDoc
	 */
	public function setHand(CardPool $hand): PlayerInterface
	{
		$this->hand = $hand;

		return $this;
	}

	/**
	 * @inheritDoc
	 */
	public function getHand(): CardPool
	{
		return $this->hand;
	}

	/**
	 * @inheritDoc
	 */
	public function setMelds(Melds $melds): PlayerInterface
	{
		$this->melds = $melds;

		return $this;
	}

	/**
	 * @inheritDoc
	 */
	public function getMelds(): Melds
	{
		return $this->melds;
	}

	/**
	 * @inheritDoc
	 */
	public function hasMelds(): bool
	{
		return count($this->melds) > 0;
	}

	/**
	 * @inheritDoc
	 */
	public function equals(PlayerInterface $player): bool
	{
		return $player instanceof Player && $player->getId() == $this->id;
	}
}