<?php

declare(strict_types=1);

namespace App\Game\Meta;

interface MetaLoaderInterface
{
	/**
	 * @return GameMeta[]
	 */
	function getAll(): array;
}