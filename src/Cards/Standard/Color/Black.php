<?php

declare(strict_types=1);

namespace App\Cards\Standard\Color;

use App\Deck\Card\Color\ColorInterface;

class Black implements ColorInterface
{
	const CODE = 'K';

	public function getHex(): string
	{
		return '000000';
	}

	function getName(): string
	{
		return 'black';
	}

	function getNameShort(): string
	{
		return self::CODE;
	}
}