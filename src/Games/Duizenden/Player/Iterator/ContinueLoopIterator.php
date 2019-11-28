<?php

declare(strict_types=1);

namespace App\Games\Duizenden\Player\Iterator;

use App\Games\Duizenden\Player\PlayerInterface;
use Iterator;

class ContinueLoopIterator implements Iterator
{
	/**
	 * @var PlayerInterface[]
	 */
	private array $players;

	/**
	 * @var int[]
	 */
	private array $keys;

	private int $pointer = 0;

	/**
	 * @param PlayerInterface[] $players
	 */
	public function __construct(array &$players, int $pointer)
	{
		$this->players = $players;
		$this->keys = array_keys($players);

		$this->reorderKeys($pointer);
	}

	private function reorderKeys(int $pointer): void
	{
		while (!$this->keys[0] == $pointer)
		{
			array_unshift($this->keys, array_pop($this->keys));
		}
	}

	public function current(): PlayerInterface
	{
		return $this->players[$this->keys[$this->pointer]];
	}

	public function next(): void
	{
		++$this->pointer;
	}

	public function key(): int
	{
		return $this->pointer;
	}

	public function valid(): bool
	{
		return array_key_exists($this->pointer, $this->keys);
	}

	public function rewind(): void
	{
		$this->pointer = 0;
	}
}
