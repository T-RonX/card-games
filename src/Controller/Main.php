<?php

namespace App\Controller;

use App\Game\Meta\GameMetaLoader;
use App\Games\Duizenden\Game;
use Exception;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\SessionInterface;

class Main extends AbstractController
{
	/**
	 * @var GameMetaLoader
	 */
	private $game_meta_loader;

	/**
	 * @var SessionInterface
	 */
	private $session;

	/**
	 * @param GameMetaLoader $game_meta_loader
	 * @param SessionInterface $session
	 */
	public function __construct(
		GameMetaLoader $game_meta_loader,
		SessionInterface $session
	)
	{
		$this->game_meta_loader = $game_meta_loader;
		$this->session = $session;
	}

	/**
	 * @return Response
	 */
	public function saved(): Response
	{
		return $this->render('Game\saved.html.twig', [
			'game_metas' => $this->game_meta_loader->getAll()
		]);
	}

	/**
	 * @param string $game_name
	 * @param string $uuid
	 *
	 * @return Response
	 *
	 * @throws Exception
	 */
	public function load(string $game_name, string $uuid): Response
	{
		$this->session->set('game_id', $uuid);

		switch ($game_name)
		{
			case Game::NAME;
				return $this->redirect($this->generateUrl('duizenden.play', [
					'uuid' => $uuid
				]));
				break;

			default:
				throw new Exception(sprintf("Game '%s' with id '%s' could not be loaded.", $game_name, $uuid));
		}
	}
}