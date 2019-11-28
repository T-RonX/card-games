<?php

declare(strict_types=1);

namespace App\Games\Duizenden\StateCompiler\Action;

use App\Games\Duizenden\StateCompiler\AbstractAction;
use App\Games\Duizenden\StateCompiler\ActionType;

class DrawFromUndrawnAndMeldAction extends AbstractAction
{
	public function __construct()
	{
		parent::__construct(ActionType::DRAW_FROM_DISCARDED_AND_MELD(), 'Draw from undrawn and meld', 'Draw a card from the undrawn card stack and meld cards.');
	}
}
