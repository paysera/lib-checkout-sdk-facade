<?php

declare(strict_types=1);

namespace Paysera\CheckoutSdk\Validator;

use Paysera\CheckoutSdk\Entity\PaymentMethodRequest;
use Paysera\CheckoutSdk\Entity\RequestInterface;
use Paysera\CheckoutSdk\Exception\CheckoutIntegrationException;

class PaymentMethodRequestValidator implements RequestValidatorInterface
{
    protected CountryCodeIso2Validator $countryCodeIso2Validator;

    public function __construct()
    {
        $this->countryCodeIso2Validator = new CountryCodeIso2Validator();
    }

    public function canValidate(RequestInterface $request): bool
    {
        return $request instanceof PaymentMethodRequest;
    }

    /**
     * @param PaymentMethodRequest $request
     * @throws CheckoutIntegrationException
     */
    public function validate(RequestInterface $request): void
    {
        if ($this->canValidate($request) === false) {
            CheckoutIntegrationException::throwInvalidType(PaymentMethodRequest::class);
        }

        $this->validateSelectedCountries($request);
    }

    /**
     * @throws CheckoutIntegrationException
     */
    protected function validateSelectedCountries(PaymentMethodRequest $request): void
    {
        foreach ($request->getSelectedCountries() as $country) {
            $this->countryCodeIso2Validator->validate($country);
        }
    }
}
