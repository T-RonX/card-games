<?php

declare(strict_types=1);

namespace App\Games\Duizenden\StateCompiler\Action;

use App\Games\Duizenden\StateCompiler\AbstractAction;
use App\Games\Duizenden\StateCompiler\ActionType;

class DrawFromUndrawnAction extends AbstractAction
{
	public function __construct()
	{
		parent::__construct(ActionType::DRAW_FROM_UNDRAWN(), 'Draw from undrawn', 'Draw a card from the undrawn card stack.');
	}
}
