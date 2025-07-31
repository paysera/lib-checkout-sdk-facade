<?php

declare(strict_types=1);

namespace Paysera\CheckoutSdk\Tests\Provider\WebToPay\Adapter;

use Paysera\CheckoutSdk\Provider\WebToPay\Adapter\PaymentValidationResponseNormalizer;
use Paysera\CheckoutSdk\Tests\AbstractCase;

class PaymentValidationResponseNormalizerTest extends AbstractCase
{
    public function testDenormalize(): void
    {
        /** @var PaymentValidationResponseNormalizer $normalizer */
        $normalizer = $this->container->get(PaymentValidationResponseNormalizer::class);
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

        $paymentValidationResponse = $normalizer->denormalize($providerData);

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
                'language' => null,
                'country' => 'lt',
                'name' => 'name',
                'surname' => null,
                'paymentCountry' => null,
                'payerIpCountry' => null,
                'payerCountry' => null,
                'paymentAmount' => 1000,
                'paymentCurrency' => 'USD',
            ],
            [
                'projectId' => $paymentValidationResponse->getProjectId(),
                'status' => $paymentValidationResponse->getStatus(),
                'payment' => $paymentValidationResponse->getPayment(),
                'originalPaymentText' => $paymentValidationResponse->getOriginalPaymentText(),
                'paymentText' => $paymentValidationResponse->getPaymentText(),
                'test' => $paymentValidationResponse->isTest(),
                'version' => $paymentValidationResponse->getVersion(),
                'requestId' => $paymentValidationResponse->getRequestId(),
                'account' => $paymentValidationResponse->getAccount(),
                'type' => $paymentValidationResponse->getType(),
                'language' => $paymentValidationResponse->getLanguage(),
                'country' => $paymentValidationResponse->getCountry(),
                'name' => $paymentValidationResponse->getName(),
                'surname' => $paymentValidationResponse->getSurname(),
                'paymentCountry' => $paymentValidationResponse->getPaymentCountry(),
                'payerIpCountry' => $paymentValidationResponse->getPayerIpCountry(),
                'payerCountry' => $paymentValidationResponse->getPayerCountry(),
                'paymentAmount' => $paymentValidationResponse->getPaymentAmount(),
                'paymentCurrency' => $paymentValidationResponse->getPaymentCurrency(),
            ],
            'The response properties values must be equal to the data set.'
        );

        $order = $paymentValidationResponse->getOrder();

        $this->assertEquals(
            [
                'orderId' => 1,
                'currency' => 'USD',
                'amount' => 1000,
                'payerFirstName' => 'John',
                'payerLastName' => 'Doe',
                'payerEmail' => 'john.doe@paysera.net',
                'payerStreet' => 'Sun str. 41',
                'payerCity' => 'London',
                'payerState' => 'Some state',
                'payerZip' => '100',
                'payerCountryCode' => 'gb',
            ],
            [
                'orderId' => $order->getOrderId(),
                'currency' => $order->getCurrency(),
                'amount' => $order->getAmount(),
                'payerFirstName' => $order->getPayerFirstName(),
                'payerLastName' => $order->getPayerLastName(),
                'payerEmail' => $order->getPayerEmail(),
                'payerStreet' => $order->getPayerStreet(),
                'payerCity' => $order->getPayerCity(),
                'payerState' => $order->getPayerState(),
                'payerZip' => $order->getPayerZip(),
                'payerCountryCode' => $order->getPayerCountryCode(),
            ],
            'The response order properties values must be equal to the data set.'
        );
    }

    public function testDenormalizeWithRefund(): void
    {
        /** @var PaymentValidationResponseNormalizer $normalizer */
        $normalizer = $this->container->get(PaymentValidationResponseNormalizer::class);

        $providerData = [
            'projectid' => '111',
            'status' => '5',
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
            // Refund data
            'refund_timestamp' => '1722454093',
            'refund_amount' => '500',
            'refund_currency' => 'USD',
            'refund_commission_amount' => '10',
            'refund_commission_currency' => 'USD',
        ];

        $response = $normalizer->denormalize($providerData);
        $refund = $response->getRefund();

        $this->assertNotNull($refund, 'Refund must be present when refund_timestamp is set');
        $this->assertSame('500', $refund->getRefundAmount());
        $this->assertSame('USD', $refund->getRefundCurrency());
        $this->assertSame('10', $refund->getRefundCommissionAmount());
        $this->assertSame('USD', $refund->getRefundCommissionCurrency());
        $this->assertSame(1722454093, $refund->getRefundTimestamp());
        $this->assertSame(5, $response->getStatus());
    }
}
