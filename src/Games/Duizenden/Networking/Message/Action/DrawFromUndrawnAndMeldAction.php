<?php

namespace App\Games\Duizenden\Networking\Message\Action;

use App\Games\Duizenden\Networking\Message\AbstractAction;
use App\Games\Duizenden\Networking\Message\ActionType;

class DrawFromUndrawnAndMeldAction extends AbstractAction
{
	public function __construct()
	{
		parent::__construct(ActionType::DRAW_FROM_DISCARDED_AND_MELD(), 'Draw from undrawn and meld', 'Draw a card from the undrawn card stack and meld cards.');
	}
}
