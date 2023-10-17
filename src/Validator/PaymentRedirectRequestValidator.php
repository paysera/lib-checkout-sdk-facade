<?php

namespace Paysera\CheckoutSdk\Validator;

use Paysera\CheckoutSdk\Entity\PaymentRedirectRequest;
use Paysera\CheckoutSdk\Entity\RequestInterface;
use Paysera\CheckoutSdk\Exception\CheckoutIntegrationException;

class PaymentRedirectRequestValidator implements RequestValidatorInterface
{
    protected CountryCodeIso2Validator $countryCodeIso2Validator;

    public function __construct()
    {
        $this->countryCodeIso2Validator = new CountryCodeIso2Validator();
    }

    public function canValidate(RequestInterface $request): bool
    {
        return $request instanceof PaymentRedirectRequest;
    }

    /**
     * @param PaymentRedirectRequest $request
     * @throws CheckoutIntegrationException
     */
    public function validate(RequestInterface $request): void
    {
        if ($this->canValidate($request) === false) {
            CheckoutIntegrationException::throwInvalidType(PaymentRedirectRequest::class);
        }

        if ($request->getCountry() !== null) {
            $this->countryCodeIso2Validator->validate($request->getCountry());
        }
        if ($request->getOrder()->getPaymentCountryCode() !== null) {
            $this->countryCodeIso2Validator->validate($request->getOrder()->getPaymentCountryCode());
        }
    }
}
