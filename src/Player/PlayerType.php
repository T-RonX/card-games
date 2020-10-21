<?php

declare(strict_types=1);

namespace App\Player;

use App\Enum\Enum;

/**
 * @method static PlayerType HUMAN()
 * @method static PlayerType AI()
 */
class PlayerType extends Enum
{
	private const HUMAN = 'human';
	private const AI = 'ai';
}