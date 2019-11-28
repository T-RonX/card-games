<?php

declare(strict_types=1);

namespace App\Cards\Standard\ParamConverter;

use App\Cards\Standard\Card;
use App\Cards\Standard\CardHelper;
use App\Cards\Standard\Exception\InvalidCardIdException;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Sensio\Bundle\FrameworkExtraBundle\Request\ParamConverter\ParamConverterInterface;
use Symfony\Component\HttpFoundation\Request;

class CardParamConverter implements ParamConverterInterface
{
	public function apply(Request $request, ParamConverter $configuration): bool
	{
		$card = null;
		$param = $configuration->getName();

		try
		{
			$card = CardHelper::createCardFromId($request->get($param));
			$request->attributes->set($param, $card);

		}
		catch (InvalidCardIdException $e)
		{
			if ($configuration->isOptional())
			{
				$request->attributes->set($param, null);

				return true;
			}
		}

		return true;
	}

	public function supports(ParamConverter $configuration): bool
	{
		return $configuration->getClass() == Card::class;
	}
}