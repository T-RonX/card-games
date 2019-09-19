<?php

namespace App\Games\Duizenden\Networking\Message\Action;

use App\Games\Duizenden\Networking\Message\AbstractAction;
use App\Games\Duizenden\Networking\Message\ActionType;

class MeldCardsAction extends AbstractAction
{
	public function __construct()
	{
		parent::__construct(ActionType::MELD_CARDS(), 'Meld cards', 'Create a set of cards from the hand.');
	}
}
