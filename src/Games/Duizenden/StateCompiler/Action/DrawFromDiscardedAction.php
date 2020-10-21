<?php

declare(strict_types=1);

namespace App\Games\Duizenden\StateCompiler\Action;

use App\Games\Duizenden\StateCompiler\AbstractAction;
use App\Games\Duizenden\Actions\ActionType;

class DrawFromDiscardedAction extends AbstractAction
{
	public function __construct()
	{
		parent::__construct(ActionType::DRAW_FROM_DISCARDED(), 'Draw from discarded', 'Draw a card from the discarded card stack.');
	}
}
