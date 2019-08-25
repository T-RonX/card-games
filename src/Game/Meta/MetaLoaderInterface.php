<?php

namespace App\Game\Meta;

interface MetaLoaderInterface
{
	/**
	 * @return GameMeta[]
	 */
	function getAll(): array;
}