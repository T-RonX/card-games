<?php

namespace App\Games\Duizenden;

use App\Game\Meta\GameMeta;
use App\Game\Meta\MetaLoaderInterface;
use App\Games\Duizenden\Repository\GameRepository;

class MetaLoader implements MetaLoaderInterface
{
	/**
	 * @var GameRepository
	 */
	private $game_repository;

	/**
	 * @inheritDoc
	 */
	public function __construct(GameRepository $game_meta_repository)
	{
		$this->game_repository = $game_meta_repository;
	}

	/**
	 * @inheritDoc
	 */
	function getAll(): array
	{
		$result = [];

		foreach ($this->game_repository->getAllLatestGames() as $game)
		{
			$result[] = new GameMeta(Game::NAME, $game->getGameMeta()->getUuid(), $game->getCreatedAt(), $game->getWorkflowMarking());
		}

		return $result;
	}
}