<?php

namespace App\Games\Duizenden\Entity;

use App\Entity\Player;
use Doctrine\Common\Collections\ArrayCollection;

class GamePlayerMeta
{
    private $shuffle_count_min;

    private $shuffle_count_max;

    private $grab_cards_min;

    private $grab_cards_max;

    private $max_inserts;

    private $id;

    private $Player;

    private $GameMeta;

    private $GamePlayers;

    public function __construct()
	{
		$this->GamePlayers = new ArrayCollection();
	}

	public function getShuffleCountMin(): ?int
    {
        return $this->shuffle_count_min;
    }

    public function setShuffleCountMin(int $shuffle_count_min): self
    {
        $this->shuffle_count_min = $shuffle_count_min;

        return $this;
    }

    public function getShuffleCountMax(): ?int
    {
        return $this->shuffle_count_max;
    }

    public function setShuffleCountMax(int $shuffle_count_max): self
    {
        $this->shuffle_count_max = $shuffle_count_max;

        return $this;
    }

    public function getGrabCardsMin()
    {
        return $this->grab_cards_min;
    }

    public function setGrabCardsMin($grab_cards_min): self
    {
        $this->grab_cards_min = $grab_cards_min;

        return $this;
    }

    public function getGrabCardsMax()
    {
        return $this->grab_cards_max;
    }

    public function setGrabCardsMax($grab_cards_max): self
    {
        $this->grab_cards_max = $grab_cards_max;

        return $this;
    }

    public function getMaxInserts(): ?int
    {
        return $this->max_inserts;
    }

    public function setMaxInserts(int $max_inserts): self
    {
        $this->max_inserts = $max_inserts;

        return $this;
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getPlayer(): ?Player
    {
        return $this->Player;
    }

    public function setPlayer(?Player $Player): self
    {
        $this->Player = $Player;

        return $this;
    }

    public function getGameMeta(): ?GameMeta
    {
        return $this->GameMeta;
    }

    public function setGameMeta(GameMeta $GameMeta): self
    {
        $this->GameMeta = $GameMeta;

        return $this;
    }
}
