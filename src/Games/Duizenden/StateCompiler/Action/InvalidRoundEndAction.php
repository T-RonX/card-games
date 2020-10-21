<?php

declare(strict_types=1);

namespace App\Games\Duizenden\StateCompiler\Action;

use App\Games\Duizenden\StateCompiler\AbstractAction;
use App\Games\Duizenden\Actions\ActionType;

class InvalidRoundEndAction extends AbstractAction
{
	public function __construct()
	{
		parent::__construct(ActionType::INVALID_ROUND_END(), 'Invalid round end', 'A round can not end in the first turn.');
	}
}
