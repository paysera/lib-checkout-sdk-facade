<?php

declare(strict_types=1);

namespace Paysera\CheckoutSdk;

use Paysera\CheckoutSdk\Entity\Collection\PaymentMethodCountryCollection;
use Paysera\CheckoutSdk\Entity\PaymentMethodCountry;
use Paysera\CheckoutSdk\Entity\PaymentMethodRequest;
use Paysera\CheckoutSdk\Entity\PaymentRedirectRequest;
use Paysera\CheckoutSdk\Entity\PaymentValidationRequest;
use Paysera\CheckoutSdk\Entity\PaymentValidationResponse;
use Paysera\CheckoutSdk\Provider\ProviderInterface;
use Paysera\CheckoutSdk\Validator\RequestValidatorInterface;

final class CheckoutFacade
{
    private ProviderInterface $provider;
    private RequestValidatorInterface $requestValidator;

    public function __construct(
        ProviderInterface $provider,
        RequestValidatorInterface $requestValidator
    ) {
        $this->provider = $provider;
        $this->requestValidator = $requestValidator;
    }

    /**
     * @param PaymentMethodRequest $request
     * @return PaymentMethodCountryCollection<PaymentMethodCountry>
     */
    public function getPaymentMethodCountries(PaymentMethodRequest $request): PaymentMethodCountryCollection
    {
        $this->requestValidator->validate($request);

        $paymentMethodCountries = $this->provider->getPaymentMethodCountries($request);

        if (count($request->getSelectedCountries()) > 0) {
            $paymentMethodCountries = $paymentMethodCountries->filterByCountryCodes($request->getSelectedCountries());
        }

        return $paymentMethodCountries;
    }

    public function redirectToPayment(PaymentRedirectRequest $request): void
    {
        $this->requestValidator->validate($request);

        $this->provider->redirectToPayment($request);
    }

    public function validatePayment(PaymentValidationRequest $request): PaymentValidationResponse
    {
        $this->requestValidator->validate($request);

        return $this->provider->validatePayment($request);
    }
}
