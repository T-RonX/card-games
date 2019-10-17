<?php

namespace App\Decks\Standard108;

use App\Cards\Standard\Color\Red;

class DeckRed extends Deck
{
	public function __construct()
	{
		parent::__construct(new Red());
	}
}