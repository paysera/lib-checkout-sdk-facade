<?php

namespace Paysera\CheckoutSdk\Provider\WebToPay;

use Paysera\CheckoutSdk\Provider\WebToPay\Adapter\PaymentMethodAdapter;
use Paysera\CheckoutSdk\Provider\WebToPay\Adapter\PaymentMethodCountryAdapter;
use Paysera\CheckoutSdk\Provider\WebToPay\Adapter\PaymentMethodGroupAdapter;
use Paysera\CheckoutSdk\Provider\WebToPay\Adapter\PaymentValidationResponseAdapter;

class WebToPayProviderFactory
{
    public static function create(): WebToPayProvider
    {
        $paymentMethodAdapter = new PaymentMethodAdapter();
        $paymentMethodGroupAdapter = new PaymentMethodGroupAdapter($paymentMethodAdapter);
        $paymentMethodCountryAdapter = new PaymentMethodCountryAdapter($paymentMethodGroupAdapter);

        return new WebToPayProvider(
            $paymentMethodCountryAdapter,
            new PaymentValidationResponseAdapter()
        );
    }
}
