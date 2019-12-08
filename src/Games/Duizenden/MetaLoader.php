<?php

declare(strict_types=1);

namespace App\Games\Duizenden;

use App\Entity\Player;
use App\Game\Meta\GameMeta;
use App\Game\Meta\MetaLoaderInterface;
use App\Games\Duizenden\Entity\Game as GameEntity;
use App\Games\Duizenden\Repository\GameRepository;

class MetaLoader implements MetaLoaderInterface
{
	private GameRepository $game_repository;

	public function __construct(GameRepository $game_meta_repository)
	{
		$this->game_repository = $game_meta_repository;
	}

	function getAll(): array
	{
		$result = [];

		foreach ($this->game_repository->getAllLatestGames() as $game)
		{
			$result[] = $this->createGameMeta($game);
		}

		return $result;
	}

	private function createGameMeta(GameEntity $game): GameMeta
	{
		return new GameMeta(Game::NAME, $game->getGameMeta()->getUuid(), $game->getCreatedAt(), $game->getWorkflowMarking());
	}

	function getAllByPlayer(Player $player): array
	{
		$result = [];

		foreach ($this->game_repository->getAllLatestGamesByPlayer($player) as $game)
		{
			$result[] = $this->createGameMeta($game);
		}

		return $result;	}
}