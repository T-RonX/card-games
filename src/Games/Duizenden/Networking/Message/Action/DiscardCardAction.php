<?php

namespace App\Games\Duizenden\Networking\Message\Action;

use App\Games\Duizenden\Networking\Message\AbstractAction;
use App\Games\Duizenden\Networking\Message\ActionType;

class DiscardCardAction extends AbstractAction
{
	public function __construct()
	{
		parent::__construct(ActionType::DISCARD_CARD(), 'Discard a card', 'Discard a card from the hand to the discarded stack.');
	}
}
