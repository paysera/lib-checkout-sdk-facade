<?php

declare(strict_types=1);

namespace Paysera\CheckoutSdk\Provider;

use Paysera\CheckoutSdk\Entity\Collection\PaymentMethodCountryCollection;
use Paysera\CheckoutSdk\Entity\PaymentMethodCountry;
use Paysera\CheckoutSdk\Entity\PaymentMethodRequest;
use Paysera\CheckoutSdk\Entity\PaymentRedirectRequest;
use Paysera\CheckoutSdk\Entity\PaymentCallbackValidationRequest;
use Paysera\CheckoutSdk\Entity\PaymentCallbackValidationResponse;
use Paysera\CheckoutSdk\Entity\PaymentRedirectResponse;

interface ProviderInterface
{
    /**
     * @param PaymentMethodRequest $request
     * @return PaymentMethodCountryCollection<PaymentMethodCountry>
     */
    public function getPaymentMethodCountries(PaymentMethodRequest $request): PaymentMethodCountryCollection;

    public function redirectToPayment(PaymentRedirectRequest $request): PaymentRedirectResponse;

    public function getPaymentCallbackValidationData(
        PaymentCallbackValidationRequest $request
    ): PaymentCallbackValidationResponse;
}
