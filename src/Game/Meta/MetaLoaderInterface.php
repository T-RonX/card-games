<?php

declare(strict_types=1);

namespace App\Game\Meta;

use App\Entity\Player;

interface MetaLoaderInterface
{
	/**
	 * @return GameMeta[]
	 */
	function getAll(): array;

	/**
	 * @return GameMeta[]
	 */
	function getAllByPlayer(Player $player): array;
}