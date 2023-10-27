<?php

declare(strict_types=1);

namespace Paysera\CheckoutSdk;

use Paysera\CheckoutSdk\Util\Container;

class CheckoutFacadeFactory
{
    public function create(ConfigProvider $configProvider = null): CheckoutFacade
    {
        return (new Container($configProvider))->get(CheckoutFacade::class);
    }
}
