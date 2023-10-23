<?php

declare(strict_types=1);

namespace Paysera\CheckoutSdk\Tests\Util;

use Paysera\CheckoutSdk\Tests\AbstractCase;
use Paysera\CheckoutSdk\Entity\Order;
use Paysera\CheckoutSdk\Util\Invader;

class InvaderTest extends AbstractCase
{
    public function testGetProperties(): void
    {
        $object = new Order(1, 1.0, 'test');
        $properties = Invader::getProperties($object);

        $this->assertEquals(
            [
                'orderId' => 1,
                'currency' => 'TEST',
                'amount' => 1.0,
                'paymentFirstName' => null,
                'paymentLastName' => null,
                'paymentEmail' => null,
                'paymentStreet' => null,
                'paymentCity' => null,
                'paymentState' => null,
                'paymentZip' => null,
                'paymentCountryCode' => null,
            ],
            $properties,
            'Stolen properties must be equal to the data set.'
        );
    }
}
