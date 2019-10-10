<?php

namespace App\Cards\Standard\Color;

use App\Deck\Card\Color\ColorInterface;

class Blue implements ColorInterface
{
	/**
	 * @inheritDoc
	 */
	public function getHex(): string
	{
		return '0000ff';
	}

	/**
	 * @inheritDoc
	 */
	function getName(): string
	{
		return 'blue';
	}
}