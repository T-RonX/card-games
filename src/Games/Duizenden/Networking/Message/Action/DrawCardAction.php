<?php

namespace App\Games\Duizenden\Networking\Message\Action;

use App\Games\Duizenden\Networking\Message\AbstractAction;
use App\Games\Duizenden\Networking\Message\ActionType;

class DrawCardAction extends AbstractAction
{
	public function __construct()
	{
		parent::__construct(ActionType::DRAW_CARD(), 'Draw a card', 'Draw a card from the undrawn stack or the discarded stack.');
	}
}
