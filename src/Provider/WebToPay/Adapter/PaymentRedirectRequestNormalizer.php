<?php

declare(strict_types=1);

namespace Paysera\CheckoutSdk\Provider\WebToPay\Adapter;

use Paysera\CheckoutSdk\Entity\Request\PaymentRedirectRequest;
use Paysera\CheckoutSdk\Provider\WebToPay\Helper\ApiVersionHelper;

class PaymentRedirectRequestNormalizer
{
    private ApiVersionHelper $apiVersionHelper;

    public function __construct(ApiVersionHelper $apiVersionHelper)
    {
        $this->apiVersionHelper = $apiVersionHelper;
    }

    public function normalize(PaymentRedirectRequest $request): array
    {
        $paymentData = [
            'projectid' => $request->getProjectId(),
            'sign_password' => $request->getProjectPassword(),
            'orderid' => $request->getOrder()->getOrderId(),
            'amount' => $request->getOrder()->getAmount(),
            'currency' => $request->getOrder()->getCurrency(),
            'accepturl' => $request->getAcceptUrl(),
            'cancelurl' => $request->getCancelUrl(),
            'callbackurl' => $request->getCallbackUrl(),
            'version' => $request->getVersion() ?? $this->apiVersionHelper->getApiVersion(),
            'lang' => $request->getLanguage(),
            'payment' => $request->getPayment(),
            'country' => $request->getCountry(),
            'paytext' => $request->getPaymentText(),
            'p_firstname' => $request->getOrder()->getPayerFirstName(),
            'p_lastname' => $request->getOrder()->getPayerLastName(),
            'p_email' => $request->getOrder()->getPayerEmail(),
            'p_street' => $request->getOrder()->getPayerStreet(),
            'p_city' => $request->getOrder()->getPayerCity(),
            'p_zip' => $request->getOrder()->getPayerZip(),
            'p_state' => $request->getOrder()->getPayerState(),
            'p_countrycode' => $request->getOrder()->getPayerCountryCode(),
            'test' => (int) $request->getTest(),
            'buyer_consent' => (int) $request->getBuyerConsent(),
            'time_limit' => $request->getTimeLimit(),
        ];

        return array_filter($paymentData, static fn ($value) => $value !== null);
    }
}
