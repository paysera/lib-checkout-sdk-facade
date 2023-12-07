<?php

declare(strict_types=1);

namespace Paysera\CheckoutSdk\Tests\Entity;

use Mockery as m;
use Paysera\CheckoutSdk\Entity\PaymentMethod;
use Paysera\CheckoutSdk\Entity\PaymentMethodGroup;
use Paysera\CheckoutSdk\Tests\AbstractCase;

class PaymentMethodGroupTest extends AbstractCase
{
    public function testIsEmpty(): void
    {
        $paymentMethodGroup = new PaymentMethodGroup('key');

        $this->assertTrue(
            $paymentMethodGroup->isEmpty(),
            'Entity without payment methods must be empty.'
        );

        $paymentMethodGroup->getPaymentMethods()->append(m::mock(PaymentMethod::class));
        $this->assertFalse(
            $paymentMethodGroup->isEmpty(),
            'Entity with payment methods must be not empty.'
        );
    }

    public function testGetFallbackTitle(): void
    {
        $paymentMethodGroup = new PaymentMethodGroup('key');

        $this->assertEquals(
            'key',
            $paymentMethodGroup->getFallbackTitle(),
            'Fallback title must be equal to group key.'
        );
    }
}
