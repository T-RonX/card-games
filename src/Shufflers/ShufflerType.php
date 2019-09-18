<?php

namespace App\Shufflers;

use App\Enum\Enum;

/**
 * @method static ShufflerType RANDOM()
 * @method static ShufflerType OVERHAND()
 */
class ShufflerType extends Enum
{
	private const RANDOM = 'random';
	private const OVERHAND = 'overhand';

}