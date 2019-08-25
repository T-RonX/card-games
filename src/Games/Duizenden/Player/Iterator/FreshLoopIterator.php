<?php

namespace App\Games\Duizenden\Player\Iterator;

use App\Games\Duizenden\Player\PlayerInterface;
use Iterator;

class FreshLoopIterator implements Iterator
{
	/**
	 * @var PlayerInterface[]
	 */
	private $players;

	/**
	 * @var int
	 */
	private $pointer = 0;

	/**
	 * @param PlayerInterface[] $players
	 */
	public function __construct(array &$players)
	{
		$this->players = $players;
	}

	/**
	 * @inheritDoc
	 */
	public function current()
	{
		return $this->players[$this->pointer];
	}

	/**
	 * @inheritDoc
	 */
	public function next()
	{
		++$this->pointer;
	}

	/**
	 * @inheritDoc
	 */
	public function key()
	{
		return $this->pointer;
	}

	/**
	 * @inheritDoc
	 */
	public function valid()
	{
		return array_key_exists($this->pointer, $this->players);
	}

	/**
	 * @inheritDoc
	 */
	public function rewind()
	{
		$this->pointer = 0;
	}
}
