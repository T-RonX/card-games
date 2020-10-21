<?php

declare(strict_types=1);

namespace App\Games\Duizenden\AI\Context;

use App\AI\Minimax\Context\ContextInterface;
use App\Games\Duizenden\Actions\ActionType;
use App\Games\Duizenden\Game;
use App\Games\Duizenden\Score\GameScore;

class Context implements ContextInterface
{
	private Game $game;
	/**
	 * @var ActionType[]
	 */
	private array $allowed_actions;
	private GameScore $game_score;
	private RiskLevel $risk_level;

	public function __construct(
		Game $game,
		array $allowed_actions,
		GameScore $game_score,
		RiskLevel $risk_level
	)
	{
		$this->game = $game;
		$this->allowed_actions = $allowed_actions;
		$this->game_score = $game_score;
		$this->risk_level = $risk_level;
	}

	public function getGame(): Game
	{
		return $this->game;
	}

	/**
	 * @param ActionType[] $allowed_actions
	 */
	public function setAllowedActions(array $allowed_actions): void
	{
		$this->allowed_actions = $allowed_actions;
	}

	/**
	 * @return ActionType[]
	 */
	public function getAllowedActions(): array
	{
		return $this->allowed_actions;
	}

	public function getGameScore(): GameScore
	{
		return $this->game_score;
	}

	public function getRiskLevel(): RiskLevel
	{
		return $this->risk_level;
	}
}