<?php

declare(strict_types=1);

namespace App\Game;

use Symfony\Component\DependencyInjection\ServiceLocator;

class GameFactory
{
	/**
	 * @var ServiceLocator|GameInterface[]
	 */
	private ServiceLocator $locator;

	public function __construct(ServiceLocator $locator)
	{
		$this->locator = $locator;
	}

	public function create(string $name): GameInterface
	{
		return $this->locator->get($name);
	}
}