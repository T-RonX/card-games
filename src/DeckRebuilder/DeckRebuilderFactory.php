<?php

namespace App\DeckRebuilder;

use App\DeckRebuilder\DeckRebuilderInterface;
use App\DeckRebuilders\DeckRebuilderType;
use Symfony\Component\DependencyInjection\ServiceLocator;

class DeckRebuilderFactory
{
	/**
	 * @var DeckRebuilderInterface[]|ServiceLocator
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
	 * @param DeckRebuilderType $algorithm
	 *
	 * @return DeckRebuilderInterface
	 */
	public function create(DeckRebuilderType $algorithm): DeckRebuilderInterface
	{
		return $this->locator->get($algorithm->getValue());
	}
}