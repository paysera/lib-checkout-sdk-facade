<?php

namespace Paysera\CheckoutSdk\Provider\WebToPay\Adapter;

use Paysera\CheckoutSdk\Entity\PaymentCallbackValidationRequest;

class PaymentCallbackValidationRequestNormalizer
{
    public function normalize(PaymentCallbackValidationRequest $request): array
    {
        $validatePaymentData = [
            'data' => $request->getData(),
            'ss1' => $request->getSs1(),
            'ss2' => $request->getSs2(),
            'type' => $request->getType(),
            'to' => $request->getTo(),
            'from' => $request->getFrom(),
            'sms' => $request->getSms(),
        ];

        return array_filter($validatePaymentData, static fn ($value) => $value !== null);
    }
}
