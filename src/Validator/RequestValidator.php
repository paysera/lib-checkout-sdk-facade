<?php

namespace Paysera\CheckoutSdk\Validator;

use Paysera\CheckoutSdk\Entity\Collection\RequestValidatorCollection;
use Paysera\CheckoutSdk\Entity\RequestInterface;

class RequestValidator
{
    /** @var RequestValidatorCollection<RequestValidatorInterface> */
    protected RequestValidatorCollection $requestValidatorCollection;

    public function __construct(
        PaymentMethodRequestValidator $paymentMethodRequestValidator,
        PaymentRedirectRequestValidator $paymentRedirectRequestValidator
    ) {
        $this->requestValidatorCollection = new RequestValidatorCollection([
            $paymentMethodRequestValidator,
            $paymentRedirectRequestValidator,
        ]);
    }

    public function canValidate(RequestInterface $request): bool
    {
        $compatibleValidatorCollection = $this->requestValidatorCollection->filter(
            static fn (RequestValidatorInterface $validator) => $validator->canValidate($request)
        );

        return (bool) $compatibleValidatorCollection->count();
    }

    public function validate(RequestInterface $request): void
    {
        foreach ($this->requestValidatorCollection as $validator) {
            if ($validator->canValidate($request)) {
                $validator->validate($request);
            }
        }
    }
}
