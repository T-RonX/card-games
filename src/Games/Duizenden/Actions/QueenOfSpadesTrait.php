<?php

declare(strict_types=1);

namespace App\Games\Duizenden\Actions;

use App\Cards\Standard\Rank\Queen;
use App\Cards\Standard\Suit\Spades;
use App\Deck\Card\CardInterface;

trait QueenOfSpadesTrait
{
    private function isCardQueenOfSpades(CardInterface $card): bool
    {
        return $card->getRank() instanceof Queen && $card->getSuit() instanceof Spades;
    }
}