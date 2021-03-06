<?php

declare(strict_types=1);

namespace App\Games\Duizenden\AI;

use App\AI\Minimax\Minimax;
use App\Games\Duizenden\AI\Context\ContextFactory;
use App\Games\Duizenden\AI\Skill\SkillLevelFactory;
use App\Games\Duizenden\Game;
use App\Games\Duizenden\Player\PlayerInterface;
use App\Games\Duizenden\State;

class AI
{
	private ContextFactory $context_factory;
	private SkillLevelFactory $skill_level_factory;
	private Minimax $minimax;

	public function __construct(
		ContextFactory $context_factory,
		SkillLevelFactory $skill_level_factory,
		Minimax $minimax
	)
	{
		$this->context_factory = $context_factory;
		$this->skill_level_factory = $skill_level_factory;
		$this->minimax = $minimax;
	}

    public function act(Game $game): void
    {
	    $player = $this->getControllingPlayer($game);
	    $skill_level = $this->skill_level_factory->create($player);
	    $context = $this->context_factory->create($game, $skill_level);

	    $action_sequence = $this->minimax->findBestActionSequence($context);
	    $action_sequence->executeSequence();
    }

    private function getControllingPlayer(Game $game): PlayerInterface
    {
	    if ($this->isGameStart($game->getState()))
	    {
	    	return $game->getState()->getDealingPlayer();
	    }

	    return $game->getState()->getPlayers()->getCurrentPlayer();
    }

    private function isGameStart(State $state): bool
    {
	    return $state->getRound() === 0;
    }
}