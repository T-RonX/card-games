<?php

declare(strict_types=1);

namespace App\Games\Duizenden\AI\Context;

use App\Games\Duizenden\AI\Skill\SkillLevelInterface;
use App\Games\Duizenden\Game;
use App\Games\Duizenden\Score\ScoreCalculator;
use App\Games\Duizenden\StateBuilder\AllowedActions;

class ContextFactory
{
	private AllowedActions $allowed_actions;
	private ScoreCalculator $score_calculator;

	public function __construct(
		AllowedActions $allowed_actions,
		ScoreCalculator $score_calculator
	)
	{
		$this->allowed_actions = $allowed_actions;
		$this->score_calculator = $score_calculator;
	}

	public function create(Game $game, SkillLevelInterface $skill_level): Context
	{
		return new Context(
			$game,
			$this->allowed_actions->getAllowedActions($game),
			$this->score_calculator->calculateGameScore($game->getId(), $game->getState()->getRoundFinishExtraPoints()),
			new RiskLevel(1),
		);
	}
}