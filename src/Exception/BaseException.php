<?php

declare(strict_types=1);

namespace Paysera\CheckoutSdk\Exception;

use Exception;

abstract class BaseException extends Exception
{
    public const E_VALIDATION = 1;
    public const E_INVALID_TYPE = 2;
    public const E_PROVIDER_ISSUE = 3;
}
