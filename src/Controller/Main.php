<?php

declare(strict_types=1);

namespace App\Controller;

use App\Game\Meta\GameMetaLoader;
use App\Games\Duizenden\Game as Duizenden;
use App\User\User\UserProvider;
use Exception;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\SessionInterface;

class Main extends AbstractController
{
    private GameMetaLoader $game_meta_loader;
    private SessionInterface $session;
    private UserProvider $user_provider;

    public function __construct(
        GameMetaLoader $game_meta_loader,
        SessionInterface $session,
        UserProvider $user_provider
    )
    {
        $this->game_meta_loader = $game_meta_loader;
        $this->session = $session;
        $this->user_provider = $user_provider;
    }

    public function saved(): Response
    {
        $player = $this->user_provider->getPlayer();

        return $this->render('Game\saved.html.twig', [
            'game_metas' => $this->game_meta_loader->getAll($player)
        ]);
    }

    /**
     * @throws Exception
     */
    public function load(string $game_name, string $uuid): Response
    {
        $this->session->set('game_id', $uuid);

        switch ($game_name)
        {
            case Duizenden::NAME;
                return $this->redirect($this->generateUrl('duizenden.play', [
                    'uuid' => $uuid
                ]));
                break;

            default:
                throw new Exception(sprintf("Game '%s' with id '%s' could not be loaded.", $game_name, $uuid));
        }
    }
}