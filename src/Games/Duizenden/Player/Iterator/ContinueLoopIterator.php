<?php

namespace App\Games\Duizenden\Player\Iterator;

use App\Games\Duizenden\Player\PlayerInterface;
use Iterator;

class ContinueLoopIterator implements Iterator
{
	/**
	 * @var PlayerInterface[]
	 */
	private $players;

	/**
	 * @var int[]
	 */
	private $keys;

	/**
	 * @var int
	 */
	private $pointer = 0;

	/**
	 * @param PlayerInterface[] $players
	 * @param int $pointer
	 */
	public function __construct(array &$players, int $pointer)
	{
		$this->players = $players;
		$this->keys = array_keys($players);

		$this->reorderKeys($pointer);
	}

	/**
	 * @param int $pointer
	 */
	private function reorderKeys(int $pointer)
	{
		while (!$this->keys[0] == $pointer)
		{
			array_unshift($this->keys, array_pop($this->keys));
		}
	}

	/**
	 * @inheritDoc
	 */
	public function current()
	{
		return $this->players[$this->keys[$this->pointer]];
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
		return array_key_exists($this->pointer, $this->keys);
	}

	/**
	 * @inheritDoc
	 */
	public function rewind()
	{
		$this->pointer = 0;
	}
}
