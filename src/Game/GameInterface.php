<?php

declare(strict_types=1);

namespace App\Game;

interface GameInterface
{
	function getId(): ?string;

	function getName(): string;
}