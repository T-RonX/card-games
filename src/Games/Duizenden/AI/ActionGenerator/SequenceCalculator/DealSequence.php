<?php

declare(strict_types=1);

namespace App\Games\Duizenden\AI\ActionGenerator\SequenceCalculator;

use App\AI\Minimax\Action\Action;
use App\AI\Minimax\Action\ActionSequenceFactory;
use App\Games\Duizenden\Actions\ActionType;
use App\Games\Duizenden\Actions\Deal\Deal;
use App\Games\Duizenden\AI\ActionGenerator\SequenceCalculatorInterface;
use App\Games\Duizenden\AI\Context\Context;
use App\Games\Duizenden\StateBuilder\AllowedActions;
use Closure;
use Generator;

class DealSequence implements SequenceCalculatorInterface
{
	private Deal $deal;
	private ActionSequenceFactory $action_sequence_factory;
	private AllowedActions $allowed_actions;

	public function __construct(
		ActionSequenceFactory $action_sequence_factory,
		AllowedActions $allowed_actions,
		Deal $deal
	)
	{
		$this->action_sequence_factory = $action_sequence_factory;
		$this->allowed_actions = $allowed_actions;
		$this->deal = $deal;
	}

	/**
	 * {@inheritDoc}
	 */
	public function getActionSequences(Context $context): Generator
	{
		yield $this->action_sequence_factory->create($context, [
				new Action($this->getActionCallback(), true)
			]);
	}

	private function getActionCallback(): Closure
	{
		return function (Context $context): Context {
			$this->deal->deal($context->getGame());
			$context->setAllowedActions($this->allowed_actions->getAllowedActions($context->getGame()));
			return $context;
		};
	}

	/**
	 * {@inheritDoc}
	 */
	public function supports(array $actions): bool
	{
		return count($actions) === 1 && $actions[0]->equals(ActionType::DEAL());
	}
}