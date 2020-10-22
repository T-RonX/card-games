<?php

declare(strict_types=1);

namespace App\Games\Duizenden\Workflow;

use App\Games\Duizenden\Game;
use App\Games\Duizenden\Persistence\Exception\GameNotFoundException;
use App\Games\Duizenden\Persistence\GamePersistence;
use Closure;
use Doctrine\ORM\NonUniqueResultException;
use Doctrine\ORM\NoResultException;
use RuntimeException;
use Symfony\Component\Workflow\Marking;
use Symfony\Component\Workflow\MarkingStore\MarkingStoreInterface;

class PersistingMarkingStore implements MarkingStoreInterface
{
	private GamePersistence $game_persistence;
	private ?Marking $current_marking = null;
	private ?Marking $current_marking_sandboxed = null;
	private bool $is_sandboxed = false;

	public function __construct(GamePersistence $game_persistence)
	{
		$this->game_persistence = $game_persistence;
	}

	/**
	 * @inheritDoc
	 *
	 * @param Game $game
	 *
	 * @throws NonUniqueResultException
	 * @throws NoResultException
	 */
	public function getMarking($game): Marking
	{
		if ($this->is_sandboxed)
		{
			if ($this->current_marking_sandboxed === null)
			{
				throw new RuntimeException("Can not get empty marking when marking store is in sandbox mode.");
			}

			return $this->current_marking_sandboxed;
		}

		return $this->current_marking ?? $this->current_marking = new Marking([$this->game_persistence->getMarking($game) => 1]);
	}

	/**
	 * @param Game $game
	 * @param array $context
	 *
	 * @throws GameNotFoundException
	 * @throws NonUniqueResultException
	 */
	public function setMarking($game, Marking $marking, array $context = []): void
	{
		if ($this->is_sandboxed)
		{
			$this->current_marking_sandboxed = $marking;

			return;
		}

		$this->current_marking = $marking;

		$this->game_persistence->persist($game, key($marking->getPlaces()), $context);
	}

	private function setIsSandboxed(bool $is_sandboxed): void
	{
		$this->is_sandboxed = $is_sandboxed;

		if ($is_sandboxed)
		{
			$this->current_marking_sandboxed = $this->current_marking ? clone $this->current_marking : null;
		}
		else
		{
			$this->current_marking_sandboxed = null;
		}
	}

	public function isSandboxed(): bool
	{
		return $this->is_sandboxed;
	}

	public function sandbox(Closure $closure): void
	{
		$this->setIsSandboxed(true);
		$closure();
		$this->setIsSandboxed(false);
	}
}