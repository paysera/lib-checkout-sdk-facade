<?php

declare(strict_types=1);

namespace Paysera\CheckoutSdk\Provider\WebToPay\Adapter;

use Paysera\CheckoutSdk\Entity\Order;
use Paysera\CheckoutSdk\Entity\PaymentValidationResponse;

class PaymentValidationResponseAdapter
{
    public function convert(array $providerResponse): PaymentValidationResponse
    {
        $map = [
            'payment' => 'payment',
            'version' => 'version',
            'original_paytext' => 'originalPaymentText',
            'paytext' => 'paymentText',
            'country' => 'country',
            'requestid' => 'requestId',
            'name' => 'name',
            'surename' => 'sureName',
            'paycurrency' => 'paymentCurrency',
            'account' => 'account',
            'type' => 'type',
        ];

        $order = $this->getOrderFromProviderResponse($providerResponse);

        $paymentValidationResponse =  new PaymentValidationResponse(
            (int) $providerResponse['projectid'],
            $order,
            (int) $providerResponse['status']
        );

        $paymentValidationResponse->setTest(
            filter_var($providerResponse['test'] ?? null, FILTER_VALIDATE_BOOLEAN)
        );

        if (isset($providerResponse['payamount'])) {
            $paymentValidationResponse->setPaymentAmount((float) $providerResponse['payamount']);
        }

        $this->setArrayValuesToObject($paymentValidationResponse, $providerResponse, $map);

        return $paymentValidationResponse;
    }

    protected function getOrderFromProviderResponse(array $providerResponse): Order
    {
        $map = [
            'p_firstname' => 'paymentFirstName',
            'p_lastname' => 'paymentLastName',
            'p_email' => 'paymentEmail',
            'p_street' => 'paymentStreet',
            'p_city' => 'paymentCity',
            'p_zip' => 'paymentZip',
            'p_state' => 'paymentState',
            'country' => 'paymentCountryCode',
            'p_countrycode' => 'paymentCountryCode',
        ];

        $order = new Order(
            (int) $providerResponse['orderid'],
            (float) $providerResponse['amount'],
            $providerResponse['currency']
        );

        $this->setArrayValuesToObject($order, $providerResponse, $map);

        return $order;
    }

    protected function setArrayValuesToObject(object $object, array $inputArray, array $map): void
    {
        foreach ($map as $arrayKey => $objectProperty) {
            if (!empty($inputArray[$arrayKey])) {
                $setterMethodName = 'set' . ucfirst($objectProperty);
                $object->{$setterMethodName}($inputArray[$arrayKey]);
            }
        }
    }
}
