<?php

namespace App\Cards\Standard\Color;

use App\Deck\Card\Color\ColorInterface;

class Red implements ColorInterface
{
	/**
	 * @inheritDoc
	 */
	public function getHex(): string
	{
		return 'ff0000';
	}

	/**
	 * @inheritDoc
	 */
	function getName(): string
	{
		return 'red';
	}
}