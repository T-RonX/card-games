<?php

declare(strict_types=1);

namespace App\Entity;

class Session
{
    private ?int $id = null;

    private string $data;

	private int $time;

	private int $lifetime;

	private Player $Player;
}
