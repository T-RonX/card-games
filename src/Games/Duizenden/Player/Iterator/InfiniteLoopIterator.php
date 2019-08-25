<?php

namespace App\Games\Duizenden\Player\Iterator;

use App\Games\Duizenden\Player\PlayerInterface;
use Iterator;

class InfiniteLoopIterator implements Iterator
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
	public function __construct(array &$players, int $pointer)
	{
		$this->players = $players;
		$this->pointer = $pointer;
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
		$this->pointer = array_key_exists($this->pointer + 1, $this->players) ? $this->pointer + 1 : 0;
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
		return true;
	}

	/**
	 * @inheritDoc
	 */
	public function rewind()
	{
		$this->pointer = 0;
	}
}
