<?php

declare(strict_types=1);

namespace Paysera\CheckoutSdk\Tests;

use Paysera\CheckoutSdk\CheckoutFacade;
use Paysera\CheckoutSdk\CheckoutFacadeFactory;

class CheckoutFacadeFactoryTest extends AbstractCase
{
    public function testCreate(): void
    {
        $facade = CheckoutFacadeFactory::create();

        $this->assertNotNull(
            $facade,
            'Method must return object of ' . CheckoutFacade::class . ' class.'
        );
    }
}
