<?php

declare(strict_types=1);

namespace App\Cards\Standard\Color;

use App\Deck\Card\Color\ColorInterface;

class Red implements ColorInterface
{
	const CODE = 'R';

	public function getHex(): string
	{
		return 'ff0000';
	}

	function getName(): string
	{
		return 'red';
	}

	function getNameShort(): string
	{
		return self::CODE;
	}
}