<?php

declare(strict_types=1);

namespace App\Game\Meta;

use App\Entity\Player;

class GameMetaLoader
{
	/**
	 * @var MetaLoaderInterface[]
	 */
	private iterable $meta_loaders;

	/**
	 * @param MetaLoaderInterface[] $meta_loaders
	 */
	public function __construct(iterable $meta_loaders)
	{
		$this->meta_loaders = $meta_loaders;
	}

	/**
	 * @return GameMeta[]
	 */
	public function getAll(Player $player): array
	{
		$result = [];

		foreach ($this->meta_loaders as $loader)
		{
			foreach ($loader->getAllByPlayer($player) as $game)
			{
				$result[$game->getGame()][] = $game;
			}
		}

		return $result;
	}
}