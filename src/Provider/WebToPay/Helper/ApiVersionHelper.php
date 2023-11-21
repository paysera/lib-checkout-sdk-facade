<?php

declare(strict_types=1);

namespace Paysera\CheckoutSdk\Provider\WebToPay\Helper;

use WebToPay;

class ApiVersionHelper
{
    public function getApiVersion(): string
    {
        return WebToPay::VERSION;
    }
}
