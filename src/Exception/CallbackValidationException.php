<?php

declare(strict_types=1);

namespace Paysera\CheckoutSdk\Exception;

class CallbackValidationException extends BaseException
{
    public function __construct(string $message)
    {
        parent::__construct($message, static::E_CALLBACK_VALIDATION);
    }
}
