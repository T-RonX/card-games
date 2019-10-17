<?php

namespace App\Decks\Standard108;

use App\Cards\Standard\Color\Blue;

class DeckBlue extends Deck
{
	public function __construct()
	{
		parent::__construct(new Blue());
	}
}