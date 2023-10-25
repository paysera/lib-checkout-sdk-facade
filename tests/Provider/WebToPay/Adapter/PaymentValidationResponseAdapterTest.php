<?php

declare(strict_types=1);

namespace Paysera\CheckoutSdk\Tests\Provider\WebToPay\Adapter;

use Paysera\CheckoutSdk\Provider\WebToPay\Adapter\PaymentValidationResponseAdapter;
use Paysera\CheckoutSdk\Tests\AbstractCase;

class PaymentValidationResponseAdapterTest extends AbstractCase
{
    public function testConvert(): void
    {
        $adapter = new PaymentValidationResponseAdapter();
        $providerData = [
            'projectid' => '111',
            'status' => '1',
            'payment' => 'payment',
            'version' => 'version',
            'original_paytext' => 'originalPaymentText',
            'paytext' => 'paymentText',
            'country' => 'lt',
            'requestid' => 'requestId',
            'name' => 'name',
            'surename' => 'sureName',
            'payamount' => '1000',
            'paycurrency' => 'USD',
            'account' => 'account',
            'type' => 'type',
            'test' => '0',
            'orderid' => '1',
            'amount' => '1000',
            'currency' => 'USD',
            'p_firstname' => 'John',
            'p_lastname' => 'Doe',
            'p_email' => 'john.doe@paysera.net',
            'p_street' => 'Sun str. 41',
            'p_city' => 'London',
            'p_zip' => '100',
            'p_countrycode' => 'gb',
            'p_state' => 'Some state',
        ];

        $paymentValidationResponse = $adapter->convert($providerData);

        $responseProperties = $this->getObjectProperties($paymentValidationResponse);
        unset($responseProperties['order']);

        $this->assertEquals(
            [
                'projectId' => 111,
                'status' => 1,
                'payment' => 'payment',
                'originalPaymentText' => 'originalPaymentText',
                'paymentText' => 'paymentText',
                'test' => false,
                'version' => 'version',
                'requestId' => 'requestId',
                'account' => 'account',
                'type' => 'type',
            ],
            $responseProperties,
            'The response properties values must be equal to the data set.'
        );

        $orderProperties = $this->getObjectProperties($paymentValidationResponse->getOrder());

        $this->assertEquals(
            [
                'orderId' => 1,
                'currency' => 'USD',
                'amount' => 1000.0,
                'paymentFirstName' => 'John',
                'paymentLastName' => 'Doe',
                'paymentEmail' => 'john.doe@paysera.net',
                'paymentStreet' => 'Sun str. 41',
                'paymentCity' => 'London',
                'paymentState' => 'Some state',
                'paymentZip' => '100',
                'paymentCountryCode' => 'gb',
            ],
            $orderProperties,
            'The response order properties values must be equal to the data set.'
        );
    }
}
