<?php

namespace App\Games\Duizenden\Networking\Message;

use App\Games\Duizenden\Networking\Message\Action\DealAction;
use App\Games\Duizenden\Networking\Message\Action\DiscardCardAction;
use App\Games\Duizenden\Networking\Message\Action\DrawCardAction;
use App\Games\Duizenden\Networking\Message\Action\ExtendMeldAction;
use App\Games\Duizenden\Networking\Message\Action\MeldCardsAction;
use App\Games\Duizenden\Networking\Message\Action\ReorderCardsAction;

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

			case ActionType::DRAW_CARD:
				return new DrawCardAction();

			case ActionType::MELD_CARDS:
				return new MeldCardsAction();

			case ActionType::EXTEND_MELD:
				return new ExtendMeldAction();

			case ActionType::DISCARD_CARD:
				return new DiscardCardAction();

			case ActionType::REORDER_CARDS:
				return new ReorderCardsAction();

			default:
				throw new InvalidActionException(sprintf("Action type '%s' is not valid in this context.", $type->getValue()));
		}
	}
}