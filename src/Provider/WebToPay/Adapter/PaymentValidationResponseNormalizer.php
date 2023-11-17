<?php

declare(strict_types=1);

namespace Paysera\CheckoutSdk\Provider\WebToPay\Adapter;

use Paysera\CheckoutSdk\Entity\Order;
use Paysera\CheckoutSdk\Entity\PaymentCallbackValidationResponse;
use Paysera\CheckoutSdk\Util\TypeConverter;

class PaymentValidationResponseNormalizer
{
    protected TypeConverter $typeConverter;

    public function __construct(TypeConverter $typeConverter)
    {
        $this->typeConverter = $typeConverter;
    }

    public function denormalize(array $providerResponse): PaymentCallbackValidationResponse
    {
        $order = $this->getOrderFromProviderResponse($providerResponse);

        $paymentValidationResponse = new PaymentCallbackValidationResponse(
            $this->getProviderProperty('projectid', $providerResponse, TypeConverter::INT),
            $order,
            $this->getProviderProperty('status', $providerResponse, TypeConverter::INT),
        );

        return $paymentValidationResponse->setPayment($this->getProviderProperty('payment', $providerResponse))
            ->setPaymentText($this->getProviderProperty('paytext', $providerResponse))
            ->setOriginalPaymentText($this->getProviderProperty('original_paytext', $providerResponse))
            ->setVersion($this->getProviderProperty('version', $providerResponse))
            ->setRequestId($this->getProviderProperty('requestid', $providerResponse))
            ->setAccount($this->getProviderProperty('account', $providerResponse))
            ->setType($this->getProviderProperty('type', $providerResponse))
            ->setTest($this->getProviderProperty('test', $providerResponse, TypeConverter::BOOL))
        ;
    }

    protected function getOrderFromProviderResponse(array $providerResponse): Order
    {
        $order = new Order(
            $this->getProviderProperty('orderid', $providerResponse, TypeConverter::INT),
            $this->getProviderProperty('amount', $providerResponse, TypeConverter::INT),
            $this->getProviderProperty('currency', $providerResponse),
        );

        return $order->setPaymentFirstName($this->getProviderProperty('p_firstname', $providerResponse))
            ->setPaymentLastName($this->getProviderProperty('p_lastname', $providerResponse))
            ->setPaymentEmail($this->getProviderProperty('p_email', $providerResponse))
            ->setPaymentStreet($this->getProviderProperty('p_street', $providerResponse))
            ->setPaymentCity($this->getProviderProperty('p_city', $providerResponse))
            ->setPaymentZip($this->getProviderProperty('p_zip', $providerResponse))
            ->setPaymentState($this->getProviderProperty('p_state', $providerResponse))
            ->setPaymentCountryCode($this->getProviderProperty('p_countrycode', $providerResponse))
        ;
    }

    protected function getProviderProperty(
        string $propertyName,
        array $providerResponse,
        int $convertToType = TypeConverter::DEFAULT
    ) {
        if (isset($providerResponse[$propertyName]) === false) {
            return null;
        }

        return $this->typeConverter->convert($providerResponse[$propertyName] ?? null, $convertToType);
    }
}
