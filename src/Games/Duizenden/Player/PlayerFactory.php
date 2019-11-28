<?php

declare(strict_types=1);

namespace App\Games\Duizenden\Player;

use App\CardPool\CardPool;
use App\Common\Meld\Melds;
use App\Repository\PlayerRepository;
use App\Shuffler\ShufflerFactory;
use App\Shuffler\ShufflerInterface;
use App\Shufflers\ShufflerType;

class PlayerFactory
{
	private ShufflerFactory $shuffler_factory;

	private PlayerRepository $player_repository;

	public function __construct(
		ShufflerFactory $shuffler_factory,
		PlayerRepository $player_repository
	)
	{
		$this->shuffler_factory = $shuffler_factory;
		$this->player_repository = $player_repository;
	}

	public function create(
		string $uuid,
		CardPool $hand = null,
		Melds $melds = null,
		ShufflerInterface $shuffler = null
	): PlayerInterface
	{
		$player = $this->player_repository->findOneByUuid($uuid);

		return (new Player())
			->setId($player->getUuid())
			->setName($player->getName())
			->setHand($hand ?? new CardPool())
			->setMelds($melds ?? new Melds())
			->setShuffler($shuffler ?? $this->shuffler_factory->create(ShufflerType::OVERHAND()));
	}

	private function generateId(): int
	{
		static $id = 0;

		return ++$id;
	}
}