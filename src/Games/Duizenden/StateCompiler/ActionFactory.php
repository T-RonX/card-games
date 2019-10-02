<?php

namespace App\Games\Duizenden\StateCompiler;

use App\Games\Duizenden\StateCompiler\Action\DealAction;
use App\Games\Duizenden\StateCompiler\Action\DiscardEndGameAction;
use App\Games\Duizenden\StateCompiler\Action\DiscardEndRoundAction;
use App\Games\Duizenden\StateCompiler\Action\DiscardEndTurnAction;
use App\Games\Duizenden\StateCompiler\Action\DrawFromDiscardedAction;
use App\Games\Duizenden\StateCompiler\Action\DrawFromUndrawnAction;
use App\Games\Duizenden\StateCompiler\Action\DrawFromUndrawnAndMeldAction;
use App\Games\Duizenden\StateCompiler\Action\ExtendMeldAction;
use App\Games\Duizenden\StateCompiler\Action\MeldCardsAction;
use App\Games\Duizenden\StateCompiler\Action\ReorderCardsAction;

class ActionFactory
{
	/**
	 * @param ActionType $type
	 *
	 * @return ActionInterface
	 *
	 * @throws InvalidActionException
	 */
	public function create(ActionType $type): ActionInterface
	{
		switch ($type->getValue())
		{
			case ActionType::DEAL:
				return new DealAction();

			case ActionType::DRAW_FROM_UNDRAWN:
				return new DrawFromUndrawnAction();

			case ActionType::DRAW_FROM_DISCARDED:
				return new DrawFromDiscardedAction();

			case ActionType::DRAW_FROM_DISCARDED_AND_MELD:
				return new DrawFromUndrawnAndMeldAction();

			case ActionType::MELD_CARDS:
				return new MeldCardsAction();

			case ActionType::EXTEND_MELD:
				return new ExtendMeldAction();

			case ActionType::DISCARD_END_TURN:
				return new DiscardEndTurnAction();

			case ActionType::DISCARD_END_ROUND:
				return new DiscardEndRoundAction();

			case ActionType::DISCARD_END_GAME:
				return new DiscardEndGameAction();

			case ActionType::REORDER_CARDS:
				return new ReorderCardsAction();

			default:
				throw new InvalidActionException(sprintf("Action type '%s' is not valid in this context.", $type->getValue()));
		}
	}
}