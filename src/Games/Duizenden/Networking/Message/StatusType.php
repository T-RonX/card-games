<?php

namespace App\Games\Duizenden\Networking\Message;

use App\Enum\Enum;

/**
 * @method static StatusType OK()
 * @method static StatusType FAILED()
 */
class StatusType extends Enum
{
	private const OK = 'ok';
	private const FAILED = 'failed';
}