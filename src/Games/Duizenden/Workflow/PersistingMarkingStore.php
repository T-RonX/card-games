<?php

declare(strict_types=1);

namespace App\Games\Duizenden\Workflow;

use App\Games\Duizenden\Game;
use App\Games\Duizenden\Persistence\Exception\GameNotFoundException;
use App\Games\Duizenden\Persistence\GamePersistence;
use Doctrine\ORM\NonUniqueResultException;
use Doctrine\ORM\NoResultException;
use Symfony\Component\Workflow\Marking;
use Symfony\Component\Workflow\MarkingStore\MarkingStoreInterface;

class PersistingMarkingStore implements MarkingStoreInterface
{
	private GamePersistence $game_persistence;

	private ?Marking $current_marking = null;

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
		$this->current_marking = $marking;

		$this->game_persistence->persist($game, key($marking->getPlaces()), $context);
	}
}