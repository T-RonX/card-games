<?php

namespace App\Game;

interface GameInterface
{
	/**
	 * @return string|null
	 */
	function getId(): ?string;

	/**
	 * @return string
	 */
	function getName(): string;
}