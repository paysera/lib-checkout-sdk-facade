<?php

declare(strict_types=1);

namespace Paysera\CheckoutSdk\Tests\Provider\WebToPay;

use Paysera\CheckoutSdk\Provider\ProviderInterface;
use Paysera\CheckoutSdk\Provider\WebToPay\WebToPayProviderFactory;
use Paysera\CheckoutSdk\Tests\AbstractCase;

class WebToPayProviderFactoryTest extends AbstractCase
{
    public function testCreate(): void
    {
        $provider = WebToPayProviderFactory::create();

        $this->assertInstanceOf(
            ProviderInterface::class,
            $provider,
            'Returned object must implement ' . ProviderInterface::class . ' interface.'
        );
    }
}
