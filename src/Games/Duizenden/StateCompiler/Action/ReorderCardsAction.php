<?php

namespace App\Games\Duizenden\StateCompiler\Action;

use App\Games\Duizenden\StateCompiler\AbstractAction;
use App\Games\Duizenden\StateCompiler\ActionType;

class ReorderCardsAction extends AbstractAction
{
	public function __construct()
	{
		parent::__construct(ActionType::REORDER_CARDS(), 'Reorder cards', 'Reorder the cards in a player\'s hand.');
	}
}