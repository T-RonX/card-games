<?php

declare(strict_types=1);

namespace App\Twig;

use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;

class StateDataExtension extends AbstractExtension
{
	public function getFunctions(): array
    {
        return [
            new TwigFunction('get_player_from_state_data', [$this, 'getPlayer']),
        ];
    }

    public function getPlayer(array $state_data, string $player_id): ?array
    {
        foreach ($state_data['players'] as $player)
		{
			if ($player['id'] === $player_id)
			{
				return $player;
			}
		}

        return null;
    }
}