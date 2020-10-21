<?php

declare(strict_types=1);

namespace App\Games\Duizenden\AI\Skill;

class SkillLevel implements SkillLevelInterface
{
	private int $skill_level;

	public function __construct(int $skill_level)
	{
		$this->skill_level = $skill_level;
	}

	public function getSkillLevel(): int
	{
		return $this->skill_level;
	}
}