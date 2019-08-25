<?php

namespace App\Cards\Standard\Color;

use App\Deck\Card\Color\ColorInterface;

class Black implements ColorInterface
{
	/**
	 * @inheritDoc
	 */
	public function getHex(): string
	{
		return '000000';
	}

	/**
	 * @inheritDoc
	 */
	function getName(): string
	{
		return 'black';
	}
}