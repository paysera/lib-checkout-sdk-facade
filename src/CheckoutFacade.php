<?php

declare(strict_types=1);

namespace Paysera\CheckoutSdk;

use Paysera\CheckoutSdk\Entity\Collection\PaymentMethodCountryCollection;
use Paysera\CheckoutSdk\Entity\PaymentMethodCountry;
use Paysera\CheckoutSdk\Entity\Request\PaymentMethodsRequest;
use Paysera\CheckoutSdk\Entity\Request\PaymentRedirectRequest;
use Paysera\CheckoutSdk\Entity\Request\PaymentCallbackValidationRequest;
use Paysera\CheckoutSdk\Entity\PaymentCallbackValidationResponse;
use Paysera\CheckoutSdk\Entity\PaymentRedirectResponse;
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
     * @param PaymentMethodsRequest $request
     * @return PaymentMethodCountryCollection<PaymentMethodCountry>
     */
    public function getPaymentMethodCountries(PaymentMethodsRequest $request): PaymentMethodCountryCollection
    {
        $this->requestValidator->validate($request);

        return $this->provider->getPaymentMethodCountries($request);
    }

    public function getPaymentRedirect(PaymentRedirectRequest $request): PaymentRedirectResponse
    {
        $this->requestValidator->validate($request);

        return $this->provider->getPaymentRedirect($request);
    }

    public function getPaymentCallbackValidatedData(
        PaymentCallbackValidationRequest $request
    ): PaymentCallbackValidationResponse {
        $this->requestValidator->validate($request);

        return $this->provider->getPaymentCallbackValidatedData($request);
    }
}
