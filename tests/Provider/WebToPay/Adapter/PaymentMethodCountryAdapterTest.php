<?php

declare(strict_types=1);

namespace Paysera\CheckoutSdk\Tests\Provider\WebToPay\Adapter;

use Mockery as m;
use Paysera\CheckoutSdk\Entity\PaymentMethodGroup;
use Paysera\CheckoutSdk\Provider\WebToPay\Adapter\PaymentMethodCountryAdapter;
use Paysera\CheckoutSdk\Provider\WebToPay\Adapter\PaymentMethodGroupAdapter;
use Paysera\CheckoutSdk\Tests\AbstractCase;
use Paysera\CheckoutSdk\Util\Invader;
use WebToPay_PaymentMethodCountry;
use WebToPay_PaymentMethodGroup;

class PaymentMethodCountryAdapterTest extends AbstractCase
{
    public function testConvert(): void
    {
        $paymentMethodGroup = m::mock(PaymentMethodGroup::class);
        $paymentMethodGroupAdapter = m::mock(PaymentMethodGroupAdapter::class);
        $paymentMethodGroupAdapter->shouldReceive('convert')
            ->withAnyArgs()
            ->andReturn($paymentMethodGroup);

        $adapter = new PaymentMethodCountryAdapter($paymentMethodGroupAdapter);
        $providerData = [
            'countryCode' => 'gb',
            'defaultLanguage' => 'en',
            'titleTranslations' => [
                'en' => 'en title',
                'lt' => 'lt title',
                'lv' => 'lv title',
            ],
            'groups' => [
                m::mock(WebToPay_PaymentMethodGroup::class),
                m::mock(WebToPay_PaymentMethodGroup::class),
                m::mock(WebToPay_PaymentMethodGroup::class),
            ],
        ];

        $providerEntity = m::mock(WebToPay_PaymentMethodCountry::class);
        m::mock('overload:'. Invader::class)
            ->expects()
            ->getProperties($providerEntity)
            ->andReturn($providerData);

        $paymentMethodCountry = $adapter->convert($providerEntity);

        $this->assertEquals(
            $providerData['countryCode'],
            $paymentMethodCountry->getCode(),
            static::VALUE_MUST_BE_EQUAL_TO_PROVIDER_MESSAGE
        );
        $this->assertEquals(
            $providerData['defaultLanguage'],
            $paymentMethodCountry->getDefaultLanguage(),
            static::VALUE_MUST_BE_EQUAL_TO_PROVIDER_MESSAGE
        );
        $this->assertEquals(
            count($providerData['titleTranslations']),
            $paymentMethodCountry->getTitleTranslations()->count(),
            static::COUNT_MUST_BE_EQUAL_TO_PROVIDER_MESSAGE
        );
        $this->assertEquals(
            count($providerData['groups']),
            $paymentMethodCountry->getGroups()->count(),
            static::COUNT_MUST_BE_EQUAL_TO_PROVIDER_MESSAGE
        );
    }
}
