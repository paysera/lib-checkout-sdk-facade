<?php

namespace Paysera\CheckoutSdk;

use Paysera\CheckoutSdk\CheckoutFacade;
use Paysera\CheckoutSdk\Entity\Collection\RequestValidatorCollection;
use Paysera\CheckoutSdk\Provider\WebToPay\WebToPayProviderFactory;
use Paysera\CheckoutSdk\Validator\PaymentMethodRequestValidator;
use Paysera\CheckoutSdk\Validator\PaymentRedirectRequestValidator;

class CheckoutFacadeFactory
{
    public static function create(): CheckoutFacade
    {
        $provider = WebToPayProviderFactory::create();
        $requestValidatorCollection = new RequestValidatorCollection([
            new PaymentMethodRequestValidator(),
            new PaymentRedirectRequestValidator()
        ]);

        return new CheckoutFacade($provider, $requestValidatorCollection);
    }
}
