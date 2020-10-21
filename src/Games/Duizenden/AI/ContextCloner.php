<?php

declare(strict_types=1);

namespace  App\Games\Duizenden\AI;

use App\AI\Minimax\Context\ContextClonerInterface;
use App\AI\Minimax\Context\ContextInterface;
use App\Games\Duizenden\AI\Context\Context;
use App\Games\Duizenden\Game;
use App\Games\Duizenden\GameCloner;
use App\Games\Duizenden\Score\GameScoreCloner;
use RuntimeException;

class ContextCloner implements ContextClonerInterface
{
	private GameScoreCloner $game_score_cloner;
	/**
	 * @var GameCloner
	 */
	private GameCloner $game_cloner;

	public function __construct(
		GameCloner  $game_cloner,
		GameScoreCloner $game_score_cloner
	)
	{
		$this->game_cloner = $game_cloner;
		$this->game_score_cloner = $game_score_cloner;
	}

	public function cloneContext(ContextInterface $context): ContextInterface
	{
		$context = $this->validateContext($context);

		$game = $this->game_cloner->cloneGame($context->getGame());
		$allowed_actions = $context->getAllowedActions();
		$game_score = $this->game_score_cloner->cloneGameScore($context->getGameScore());
		$risk_level = $context->getRiskLevel();

		return new Context($game, $allowed_actions, $game_score, $risk_level);
	}

	private function validateContext(ContextInterface $context): Context
	{
		if ($context instanceof Context)
		{
			return $context;
		}

		throw new RuntimeException("Unexpected context type given.");
	}

	public function supports(string $name): bool
	{
		return $name === Game::NAME;
	}
}