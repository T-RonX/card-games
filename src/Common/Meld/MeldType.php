<?php

namespace App\Common\Meld;

use App\Enum\Enum;

/**
 * @method static self RUN()
 * @method static self SET()
 */
class MeldType extends Enum
{
	private const RUN = 1;
	private const SET = 2;
}