<?php

namespace App\Games\Duizenden\Networking\Message\Action;

use App\Games\Duizenden\Networking\Message\AbstractAction;
use App\Games\Duizenden\Networking\Message\ActionType;

class ReorderCardsAction extends AbstractAction
{
	public function __construct()
	{
		parent::__construct(ActionType::REORDER_CARDS(), 'Reorder cards', 'Reorder the cards in a player\'s hand.');
	}
}
