<?php

declare(strict_types=1);

namespace App\Controller\Duizenden;

use App\Cards\Standard\Exception\InvalidCardIdException;
use App\Entity\Player;
use App\Enum\Exception\EnumConstantsCouldNotBeResolvedException;
use App\Enum\Exception\EnumNotDefinedException;
use App\Games\Duizenden\Actions\Hand\ReorderCard;
use App\Games\Duizenden\Game;
use App\Games\Duizenden\Persistence\Exception\GameNotFoundException;
use App\Games\Duizenden\Player\Exception\PlayerNotFoundException;
use App\Games\Duizenden\Player\PlayerInterface;
use App\Games\Duizenden\Score\Exception\UnmappedCardException;
use App\Games\Duizenden\Actions\ActionType;
use App\Security\Voter\Duizenden\GameVoter;
use App\User\User\UserProvider;
use Doctrine\ORM\NonUniqueResultException;
use Doctrine\ORM\ORMException;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;

class HandController extends AbstractController
{
    use LoadGameTrait;
    use NotifyPlayersTrait;

    private ReorderCard $reorder_card;
    private UserProvider $user_provider;

    public function __construct(
        ReorderCard $reorder_card,
        UserProvider $user_provider
    )
    {
        $this->reorder_card = $reorder_card;
        $this->user_provider = $user_provider;
    }

    /**
     * @throws EnumConstantsCouldNotBeResolvedException
     * @throws EnumNotDefinedException
     * @throws GameNotFoundException
     * @throws InvalidCardIdException
     * @throws NonUniqueResultException
     * @throws ORMException
     * @throws PlayerNotFoundException
     * @throws UnmappedCardException
     * @return Response
     */
    public function reorderCard(int $source, int $target): Response
    {
        $game = $this->loadGame();
        $this->denyAccessUnlessGranted(GameVoter::REORDER_CARDS, $game);

        $this->reorder_card->reorder($game, $this->getGamePlayer($game), $source - 1, $target - 1);

        $source_player = $game->getState()->getPlayers()->getPlayerById($this->user_provider->getPlayer()->getUuid());
        $this->notifyPlayers($game, $source_player, ActionType::REORDER_CARDS());

        return $this->json([]);
    }

    private function getGamePlayer(Game $game, Player $player = null): PlayerInterface
    {
        return $game->getGamePlayerById($player ? $player->getUuid() : $this->user_provider->getPlayer()->getUuid());
    }
}