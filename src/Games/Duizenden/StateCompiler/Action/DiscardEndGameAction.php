<?php

declare(strict_types=1);

namespace App\Games\Duizenden\StateCompiler\Action;

use App\Games\Duizenden\StateCompiler\AbstractAction;
use App\Games\Duizenden\Actions\ActionType;

class DiscardEndGameAction extends AbstractAction
{
	public function __construct()
	{
		parent::__construct(ActionType::DISCARD_END_GAME(), 'Discard end game', 'Last card discarded, game finished.');
	}
}
