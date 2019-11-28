<?php

declare(strict_types=1);

namespace App\Games\Duizenden\Player;

use App\CardPool\CardPool;
use App\Common\Meld\Melds;
use App\Shuffler\ShufflerInterface;

interface PlayerInterface
{
	public function setId(string $id): self;

	public function getId(): string;

	public function setName(string $name): self;

	public function getName(): string;

	public function setShuffler(ShufflerInterface $shuffler): self;

	public function getShuffler(): ShufflerInterface;

	public function setHand(CardPool $hand): self;

	public function getHand(): CardPool;

	public function setMelds(Melds $melds): self;

	public function getMelds(): Melds;

	public function hasMelds(): bool;

	public function equals(PlayerInterface $player): bool;
}