<?php

declare(strict_types=1);

namespace Entity;

use Paysera\CheckoutSdk\Entity\PaymentMethod;
use Paysera\CheckoutSdk\Tests\AbstractCase;

class PaymentMethodTest extends AbstractCase
{
    public function testGetFallbackTitle(): void
    {
        $paymentMethod = new PaymentMethod('key');

        $this->assertEquals(
            'key',
            $paymentMethod->getFallbackTitle(),
            'Fallback title must be equal to key.'
        );
    }
}
