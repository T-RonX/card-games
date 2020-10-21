<?php

declare(strict_types=1);

namespace App\AI\Minimax\Action;

use App\AI\Minimax\Context\ContextCloner;
use App\AI\Minimax\Context\ContextInterface;
use App\AI\Minimax\State\PossibleStateFactory;
use Symfony\Component\Workflow\StateMachine;

class ActionSequenceFactory
{
	private PossibleStateFactory $possible_state_factory;
	private ContextCloner $context_cloner;
	private StateMachine $state_machine;

	public function __construct(
		PossibleStateFactory $possible_state_factory,
		ContextCloner $context_cloner,
		StateMachine $state_machine
	)
	{
		$this->possible_state_factory = $possible_state_factory;
		$this->context_cloner = $context_cloner;
		$this->state_machine = $state_machine;
	}

	/**
	 * @param Action[] $actions
	 */
	public function create(ContextInterface $context, array $actions): ActionSequence
	{
		return new ActionSequence(
			$this->state_machine,
			$this->possible_state_factory,
			$this->context_cloner,
			$context,
			$actions
		);
	}
}