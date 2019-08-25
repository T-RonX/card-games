<?php

namespace App\Shuffler;

use App\Shufflers\ShufflerType;
use Symfony\Component\DependencyInjection\ServiceLocator;

class ShufflerFactory
{
	/**
	 * @var ShufflerInterface[]|ServiceLocator
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
	 * @param ShufflerType $algorithm
	 *
	 * @return ShufflerInterface
	 */
	public function create(ShufflerType $algorithm): ShufflerInterface
	{
		return $this->locator->get($algorithm->getValue());
	}
}