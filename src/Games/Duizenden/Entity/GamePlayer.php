<?php

declare(strict_types=1);

namespace App\Games\Duizenden\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;

class GamePlayer
{
	private ?int $id = null;

	private string $hand;

	private string $melds;

    private Game $Game;

    private ?GamePlayerMeta $GamePlayerMeta = null;

    private Collection $CurrentPlayerGames;

    public function __construct()
	{
		$this->CurrentPlayerGames = new  ArrayCollection();
	}

	public function getHand(): array
    {
        return json_decode($this->hand);
    }

    public function setHand(array $hand): self
    {
        $this->hand = json_encode($hand);

        return $this;
    }

    public function getMelds(): array
    {
        return json_decode($this->melds);
    }

    public function setMelds($melds): self
    {
        $this->melds = json_encode($melds);

        return $this;
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getGame(): ?Game
    {
        return $this->Game;
    }

    public function setGame(?Game $Game): self
    {
        $this->Game = $Game;

        return $this;
    }

    public function getGamePlayerMeta(): ?GamePlayerMeta
    {
        return $this->GamePlayerMeta;
    }

    public function setGamePlayerMeta(?GamePlayerMeta $GamePlayerMeta): self
    {
        $this->GamePlayerMeta = $GamePlayerMeta;

        return $this;
    }
}
