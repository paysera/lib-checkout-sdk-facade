<?php

declare(strict_types=1);

namespace Paysera\CheckoutSdk;

use Paysera\CheckoutSdk\Provider\WebToPay\WebToPayProvider;
use Paysera\CheckoutSdk\Util\Container;
use Paysera\CheckoutSdk\Validator\RequestValidator;

class CheckoutFacadeFactory
{
    public function create(): CheckoutFacade
    {
        $container = new Container();

        return new CheckoutFacade(
            $container->get(WebToPayProvider::class),
            $container->get(RequestValidator::class)
        );
    }
}
