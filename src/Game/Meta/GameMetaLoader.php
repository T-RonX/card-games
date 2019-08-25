<?php

namespace App\Game\Meta;

class GameMetaLoader
{
	/**
	 * @var iterable|MetaLoaderInterface[]
	 */
	private $meta_loaders;

	/**
	 * @param iterable|MetaLoaderInterface[] $meta_loaders
	 */
	public function __construct(iterable $meta_loaders)
	{
		$this->meta_loaders = $meta_loaders;
	}

	/**
	 * @return GameMeta[]
	 */
	public function getAll()
	{
		$result = [];

		foreach ($this->meta_loaders as $loader)
		{
			foreach ($loader->getAll() as $game)
			{
				$result[$game->getGame()][] = $game;
			}
		}

		return $result;
	}
}