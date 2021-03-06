<?php

declare(strict_types=1);

namespace App\Games\Duizenden;

use App\Games\Duizenden\Repository\GameMetaRepository;
use App\Games\Duizenden\Repository\GameRepository;

class GameDeleter
{
	private GameRepository $game_repository;

	private GameMetaRepository $meta_repository;

	public function __construct(
		GameRepository $game_repository,
		GameMetaRepository $meta_repository
	)
	{
		$this->game_repository = $game_repository;
		$this->meta_repository = $meta_repository;
	}

	public function delete(string $uuid): void
	{
		$game_meta = $this->meta_repository->findOneBy(['uuid' => $uuid]);

		if (!$game_meta)
		{
			return;
		}

		// This query prevents MySql recursive cascade delete loop limit of 15,
		$this->game_repository
			->createQueryBuilder('g')
			->update()
			->set('g.Game', 'null')
			->set('g.CurrentPlayer', 'null')
			->where('g.GameMeta = :meta')
			->setParameter('meta', $game_meta)
			->getQuery()
			->execute();

		$this->meta_repository
			->createQueryBuilder('gm')
			->delete()
			->where('gm = :game_meta')
			->setParameter('game_meta', $game_meta)
			->getQuery()
			->execute();
	}
}