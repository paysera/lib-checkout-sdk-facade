<?php

declare(strict_types=1);

namespace Paysera\CheckoutSdk\Tests\Provider\WebToPay\Adapter;

use Mockery as m;
use Paysera\CheckoutSdk\Entity\PaymentMethod;
use Paysera\CheckoutSdk\Provider\WebToPay\Adapter\PaymentMethodAdapter;
use Paysera\CheckoutSdk\Provider\WebToPay\Adapter\PaymentMethodGroupAdapter;
use Paysera\CheckoutSdk\Tests\AbstractCase;
use Paysera\CheckoutSdk\Util\Invader;
use WebToPay_PaymentMethod;
use WebToPay_PaymentMethodGroup;

class PaymentMethodGroupAdapterTest extends AbstractCase
{
    public function testConvert(): void
    {
        $providerData = [
            'groupKey' => 'group test key',
            'defaultLanguage' => 'en',
            'translations' => [
                'en' => 'en title',
                'lt' => 'lt title',
                'lv' => 'lv title',
            ],
            'paymentMethods' => [
                m::mock(WebToPay_PaymentMethod::class),
                m::mock(WebToPay_PaymentMethod::class),
                m::mock(WebToPay_PaymentMethod::class),
            ],
        ];

        $providerEntity = m::mock(WebToPay_PaymentMethodGroup::class);
        $invaderMock = m::mock(Invader::class)
            ->expects()
            ->getProperties($providerEntity)
            ->andReturn($providerData)
            ->getMock();
        $this->container->set(Invader::class, $invaderMock);

        $paymentMethod = m::mock(PaymentMethod::class);
        $paymentMethodAdapterMock = m::mock(PaymentMethodAdapter::class)
            ->shouldReceive('convert')
            ->withAnyArgs()
            ->andReturn($paymentMethod)
            ->getMock();
        $this->container->set(PaymentMethodAdapter::class, $paymentMethodAdapterMock);

        $adapter = $this->container->get(PaymentMethodGroupAdapter::class);

        $paymentMethodGroup = $adapter->convert($providerEntity);

        $this->assertEquals(
            $providerData['groupKey'],
            $paymentMethodGroup->getKey(),
            static::VALUE_MUST_BE_EQUAL_TO_PROVIDER_MESSAGE
        );
        $this->assertEquals(
            $providerData['defaultLanguage'],
            $paymentMethodGroup->getDefaultLanguage(),
            static::VALUE_MUST_BE_EQUAL_TO_PROVIDER_MESSAGE
        );
        $this->assertEquals(
            count($providerData['translations']),
            $paymentMethodGroup->getTitleTranslations()->count(),
            static::COUNT_MUST_BE_EQUAL_TO_PROVIDER_MESSAGE
        );
        $this->assertEquals(
            count($providerData['paymentMethods']),
            $paymentMethodGroup->getPaymentMethods()->count(),
            static::COUNT_MUST_BE_EQUAL_TO_PROVIDER_MESSAGE
        );
    }
}
