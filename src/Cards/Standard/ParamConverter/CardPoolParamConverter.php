<?php

namespace App\Cards\Standard\ParamConverter;

use App\CardPool\CardPool;
use App\Cards\Standard\Card;
use App\Cards\Standard\CardHelper;
use App\Cards\Standard\Exception\InvalidCardIdException;
use App\Deck\Card\CardInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Sensio\Bundle\FrameworkExtraBundle\Request\ParamConverter\ParamConverterInterface;
use Symfony\Component\HttpFoundation\Request;

class CardPoolParamConverter implements ParamConverterInterface
{
	/**
	 * @inheritDoc
	 */
	public function apply(Request $request, ParamConverter $configuration)
	{
		$cards = [];
		$param = $configuration->getName();

		try
		{
			foreach (explode(',', $request->get($param)) as $card)
			{
				$cards[] = CardHelper::createCardFromId($card);
			}

			$request->attributes->set($param, new CardPool($cards));

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

	/**
	 * @inheritDoc
	 */
	public function supports(ParamConverter $configuration)
	{
		return $configuration->getClass() == CardPool::class;
	}
}