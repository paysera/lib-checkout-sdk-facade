<?php

declare(strict_types=1);

namespace Paysera\CheckoutSdk\Tests\Entity;

use Mockery as m;
use Paysera\CheckoutSdk\Entity\PaymentMethodCountry;
use Paysera\CheckoutSdk\Entity\PaymentMethodGroup;
use Paysera\CheckoutSdk\Tests\AbstractCase;

class PaymentMethodCountryTest extends AbstractCase
{
    public function testIsEmpty(): void
    {
        $paymentMethodCountry = new PaymentMethodCountry('gb');

        $this->assertTrue(
            $paymentMethodCountry->isEmpty(),
            'Entity without groups must be empty.'
        );

        $paymentMethodCountry->getGroups()->append(m::mock(PaymentMethodGroup::class));
        $this->assertFalse(
            $paymentMethodCountry->isEmpty(),
            'Entity with groups must be not empty.'
        );
    }

    public function testGetFallbackTitle(): void
    {
        $paymentMethodCountry = new PaymentMethodCountry('gb');

        $this->assertEquals(
            'gb',
            $paymentMethodCountry->getFallbackTitle(),
            'Fallback title must be equal to country code.'
        );
    }
}
