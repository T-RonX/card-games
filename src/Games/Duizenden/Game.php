<?php

declare(strict_types=1);

namespace App\Games\Duizenden;

use App\DeckRebuilder\DeckRebuilderInterface;
use App\Game\GameInterface;
use App\Games\Duizenden\Initializer\Exception\InvalidDealerPlayerException;
use App\Games\Duizenden\Initializer\Initializer;
use App\Games\Duizenden\Player\Exception\EmptyPlayerSetException;
use App\Games\Duizenden\Player\PlayerInterface;
use App\Games\Duizenden\Score\ScoreCalculator;
use App\Games\Duizenden\Workflow\TransitionType;
use Symfony\Component\Workflow\Marking;
use Symfony\Component\Workflow\StateMachine;

class Game implements GameInterface
{
	public const NAME = 'duizenden';

	private ?string $id = null;

	private State $state;

	private DeckRebuilderInterface $deck_rebuilder;

	private Initializer $initializer;

	private ScoreCalculator $score_calculator;

	private StateMachine $state_machine;

	public function __construct(
		Initializer $initializer,
		ScoreCalculator $score_calculator,
		StateMachine $state_machine
	)
	{
		$this->initializer = $initializer;
		$this->score_calculator = $score_calculator;
		$this->state_machine = $state_machine;
	}

	/**
	 * @throws InvalidDealerPlayerException
	 * @throws EmptyPlayerSetException
	 */
	public function configure(Configurator $configurator): void
	{
		$this->state = $this->initializer->createInitialState($configurator);
		$this->deck_rebuilder = $this->initializer->getDeckRebuilderFromConfig($configurator);

		$this->state_machine->apply($this, TransitionType::CONFIGURE()->getValue());
	}

	public function getGamePlayerById(string $id): PlayerInterface
	{
		return $this->state->getPlayers()->getPlayerById($id);
	}

	public function getId(): ?string
	{
		return $this->id;
	}

	public function setId(string $id): void
	{
		$this->id = $id;
	}

	public function getScoreCalculator(): ScoreCalculator
	{
		return $this->score_calculator;
	}

	public function setState(State $state): void
	{
		$this->state = $state;
	}

	public function getState(): State
	{
		return $this->state;
	}

	public function getName(): string
	{
		return self::NAME;
	}

	public function setCreatedMarking(): void
	{
		$this->state_machine->getMarkingStore()->setMarking($this, $this->getMarking());
	}

	public function getMarking(): Marking
	{
		return $this->state_machine->getMarking($this);
	}

	public function getDeckRebuilder(): DeckRebuilderInterface
	{
		return $this->deck_rebuilder;
	}

	public function setDeckRebuilder(DeckRebuilderInterface $deck_rebuilder): void
	{
		$this->deck_rebuilder = $deck_rebuilder;
	}
}
