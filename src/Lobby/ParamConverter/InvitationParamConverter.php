<?php

declare(strict_types=1);

namespace App\Lobby\ParamConverter;

use App\Lobby\Entity\Invitation;
use App\Lobby\Repository\InvitationRepository;
use Exception;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Sensio\Bundle\FrameworkExtraBundle\Request\ParamConverter\ParamConverterInterface;
use Symfony\Component\HttpFoundation\Request;

class InvitationParamConverter implements ParamConverterInterface
{
	private InvitationRepository $repository;

	public function __construct(InvitationRepository $repository)
	{
		$this->repository = $repository;
	}

	public function apply(Request $request, ParamConverter $configuration): bool
	{
		$invitation = null;
		$param = $configuration->getName();

		try
		{
			$invitation = $this->repository->getInvitation((string) $request->get($param));
			$request->attributes->set($param, $invitation);

		}
		catch (Exception $e)
		{
			if ($configuration->isOptional())
			{
				$request->attributes->set($param, null);

				return true;
			}
		}

		return true;
	}

	/**
	 * @inheritDoc
	 */
	public function supports(ParamConverter $configuration): bool
	{
		return $configuration->getClass() === Invitation::class;
	}
}