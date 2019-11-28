<?php

declare(strict_types=1);

namespace App\Deck\Card\Color;

interface ColorInterface
{
	function getHex(): string;

	function getName(): string;

	function getNameShort(): string;
}