<?php

declare(strict_types=1);

namespace App\Shuffler;

use App\Shufflers\ShufflerType;
use Symfony\Component\DependencyInjection\ServiceLocator;

class ShufflerFactory
{
	/**
	 * @var ShufflerInterface[]|ServiceLocator
	 */
	private ServiceLocator $locator;

	/**
	 * @param ServiceLocator $locator
	 */
	public function __construct(ServiceLocator $locator)
	{
		$this->locator = $locator;
	}

	public function create(ShufflerType $algorithm): ShufflerInterface
	{
		return $this->locator->get($algorithm->getValue());
	}
}