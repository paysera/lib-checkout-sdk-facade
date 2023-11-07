<?php

namespace Paysera\CheckoutSdk\Factory;

use Paysera\CheckoutSdk\Entity\Collection\RequestValidatorCollection;
use Paysera\CheckoutSdk\Util\Container;
use Paysera\CheckoutSdk\Validator\PaymentMethodRequestValidator;
use Paysera\CheckoutSdk\Validator\PaymentRedirectRequestValidator;
use Paysera\CheckoutSdk\Validator\RequestValidatorInterface;

class RequestValidatorFactory
{
    public function create(Container $container): RequestValidatorInterface
    {
        return new RequestValidatorCollection([
            $container->get(PaymentMethodRequestValidator::class),
            $container->get(PaymentRedirectRequestValidator::class),
        ]);
    }
}
