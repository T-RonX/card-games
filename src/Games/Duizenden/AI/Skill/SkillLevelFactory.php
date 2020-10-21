<?php

declare(strict_types=1);

namespace App\Games\Duizenden\AI\Skill;

use App\Games\Duizenden\Player\PlayerInterface;

class SkillLevelFactory
{
	public function create(PlayerInterface $player): SkillLevelInterface
	{
		return new SkillLevel(1);
	}
}