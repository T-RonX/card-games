<?php

declare(strict_types=1);

namespace App\Common\Meld;

use App\Enum\Enum;

/**
 * @method static MeldType RUN()
 * @method static MeldType SET()
 */
class MeldType extends Enum
{
	private const RUN = 1;
	private const SET = 2;
}