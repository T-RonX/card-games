<?php

namespace App\Games\Duizenden\Actions;

use App\Games\Duizenden\Workflow\MarkingType;
use Symfony\Component\Workflow\StateMachine;

abstract class StateChangeAction
{
	/**
	 * @var StateMachine
	 */
	protected $state_machine;

	public function __construct(StateMachine $state_machine)
	{
		$this->state_machine = $state_machine;
	}

	/**
	 * @param MarkingType $state
	 *
	 * @return bool
	 */
	protected function isState(MarkingType $state): bool
	{
		return $this->state_machine->getMarking($this)->has($state->getValue());
	}
}