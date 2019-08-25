<?php

namespace App\Games\Duizenden\Player;

use App\CardPool\CardPool;
use App\Common\Meld\Melds;
use App\Shuffler\ShufflerInterface;

interface PlayerInterface
{
	/**
	 * @param string $id
	 *
	 * @return self
	 */
	public function setId(string $id): self;

	/**
	 * @return string
	 */
	public function getId(): string;

	/**
	 * @param string $name
	 *
	 * @return self
	 */
	public function setName(string $name): self;

	/**
	 * @return string
	 */
	public function getName(): string;

	/**
	 * @param ShufflerInterface $shuffler
	 *
	 * @return self
	 */
	public function setShuffler(ShufflerInterface $shuffler): self;

	/**
	 * @return ShufflerInterface
	 */
	public function getShuffler(): ShufflerInterface;

	/**
	 * @param CardPool $hand
	 *
	 * @return self
	 */
	public function setHand(CardPool $hand): self;

	/**
	 * @return CardPool
	 */
	public function getHand(): CardPool;

	/**
	 * @param Melds $melds
	 *
	 * @return PlayerInterface
	 */
	public function setMelds(Melds $melds): self;

	/**
	 * @return Melds
	 */
	public function getMelds(): Melds;

	/**
	 * @return bool
	 */
	public function hasMelds(): bool;

	/**
	 * @param PlayerInterface $player
	 *
	 * @return bool
	 */
	public function equals(PlayerInterface $player): bool;
}