<?php

namespace App\Games\Duizenden\Player;

use App\CardPool\CardPool;
use App\Common\Meld\Melds;
use App\Repository\PlayerRepository;
use App\Shuffler\ShufflerFactory;
use App\Shuffler\ShufflerInterface;
use App\Shufflers\ShufflerType;

class PlayerFactory
{
	/**
	 * @var ShufflerFactory
	 */
	private $shuffler_factory;

	/**
	 * @var PlayerRepository
	 */
	private $player_repository;

	/**
	 * @param ShufflerFactory $shuffler_factory
	 * @param PlayerRepository $player_repository
	 */
	public function __construct(
		ShufflerFactory $shuffler_factory,
		PlayerRepository $player_repository
	)
	{
		$this->shuffler_factory = $shuffler_factory;
		$this->player_repository = $player_repository;
	}

	/**
	 * @param string $uuid
	 * @param CardPool|null $hand
	 * @param Melds $melds
	 * @param ShufflerInterface|null $shuffler
	 *
	 * @return Player
	 */
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

	/**
	 * @return int
	 */
	private function generateId(): int
	{
		static $id = 0;

		return ++$id;
	}
}