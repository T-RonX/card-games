<?php

declare(strict_types=1);

namespace App\Games\Duizenden\Player\Iterator;

use App\Games\Duizenden\Player\PlayerInterface;
use Iterator;

class InfiniteLoopIterator implements Iterator
{
	/**
	 * @var PlayerInterface[]
	 */
	private array $players;

	private int $pointer = 0;

	/**
	 * @param PlayerInterface[] $players
	 */
	public function __construct(array &$players, int $pointer)
	{
		$this->players = $players;
		$this->pointer = $pointer;
	}

	public function current(): PlayerInterface
	{
		return $this->players[$this->pointer];
	}

	public function next(): void
	{
		$this->pointer = array_key_exists($this->pointer + 1, $this->players) ? $this->pointer + 1 : 0;
	}

	public function key(): int
	{
		return $this->pointer;
	}

	public function valid(): bool
	{
		return true;
	}

	public function rewind(): void
	{
		$this->pointer = 0;
	}
}
