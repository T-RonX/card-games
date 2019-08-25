<?php

namespace App\Game;

use Symfony\Component\DependencyInjection\ServiceLocator;

class GameFactory
{
	/**
	 * @var GameInterface[]
	 */
	private $locator;

	/**
	 * @param ServiceLocator $locator
	 */
	public function __construct(ServiceLocator $locator)
	{
		$this->locator = $locator;
	}
	/**
	 * @param string $name
	 *
	 * @return GameInterface
	 */
	public function create(string $name): GameInterface
	{
		/** @var GameInterface $game */
		$game = $this->locator->get($name);

		return $game;
	}
}