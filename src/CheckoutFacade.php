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
use Paysera\CheckoutSdk\Service\PaymentMethodCountryManager;
use Paysera\CheckoutSdk\Validator\RequestValidator;

final class CheckoutFacade
{
    private ProviderInterface $provider;
    private RequestValidator $requestValidator;

    public function __construct(
        ProviderInterface $provider,
        RequestValidator $requestValidator
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
            $paymentMethodCountries = (new PaymentMethodCountryManager())->filterCollectionByCodes(
                $paymentMethodCountries,
                $request->getSelectedCountries()
            );
        }

        return $paymentMethodCountries;
    }

    // TODO Return RedirectResponse
    public function redirectToPayment(PaymentRedirectRequest $request): void
    {
        $this->requestValidator->validate($request);

        $this->provider->redirectToPayment($request);
    }

    // TODO rename method to IfPaymentSuccess
    public function validatePayment(PaymentValidationRequest $request): PaymentValidationResponse
    {
        $this->requestValidator->validate($request);

        return $this->provider->validatePayment($request);
    }
}
