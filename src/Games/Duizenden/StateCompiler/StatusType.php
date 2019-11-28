<?php

declare(strict_types=1);

namespace App\Games\Duizenden\StateCompiler;

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