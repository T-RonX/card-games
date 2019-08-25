<?php

namespace App\Shufflers;

use App\Enum\Enum;

/**
 * @method static self RANDOM()
 * @method static self OVERHAND()
 */
class ShufflerType extends Enum
{
	private const RANDOM = 'random';
	private const OVERHAND = 'overhand';

}