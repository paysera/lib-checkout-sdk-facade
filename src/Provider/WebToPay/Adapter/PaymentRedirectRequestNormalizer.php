<?php

declare(strict_types=1);

namespace Paysera\CheckoutSdk\Provider\WebToPay\Adapter;

use Paysera\CheckoutSdk\Entity\PaymentRedirectRequest;

class PaymentRedirectRequestNormalizer
{
    public function normalize(PaymentRedirectRequest $request): array
    {
        $paymentData = [
            'projectid' => $request->getProjectId(),
            'sign_password' => $request->getProjectPassword(),
            'orderid' => $request->getOrder()->getOrderId(),
            'amount' => (int) $request->getOrder()->getAmount(),
            'currency' => $request->getOrder()->getCurrency(),
            'accepturl' => $request->getAcceptUrl(),
            'cancelurl' => $request->getCancelUrl(),
            'callbackurl' => $request->getCallbackUrl(),
            'payment' => $request->getPayment(),
            'country' => $request->getOrder()->getPaymentCountryCode(),
            'p_firstname' => $request->getOrder()->getPaymentFirstName(),
            'p_lastname' => $request->getOrder()->getPaymentLastName(),
            'p_email' => $request->getOrder()->getPaymentEmail(),
            'p_street' => $request->getOrder()->getPaymentStreet(),
            'p_city' => $request->getOrder()->getPaymentCity(),
            'p_zip' => $request->getOrder()->getPaymentZip(),
            'p_state' => $request->getOrder()->getPaymentState(),
            'p_countrycode' => $request->getOrder()->getPaymentCountryCode(),
            'test' => (int) $request->getTest(),
        ];

        return array_filter($paymentData, static fn ($value) => $value !== null);
    }
}
