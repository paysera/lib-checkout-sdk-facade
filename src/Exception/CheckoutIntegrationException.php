<?php

declare(strict_types=1);

namespace Paysera\CheckoutSdk\Exception;

use Exception;
use Throwable;

class CheckoutIntegrationException extends Exception
{
    public const E_VALIDATION = 1;
    public const E_INVALID_TYPE = 2;
    public const E_PROVIDER_ISSUE = 3;

    public static function throwValidation(string $message): void
    {
        throw new self($message, static::E_VALIDATION);
    }

    public static function throwInvalidType(string $requiredType): void
    {
        throw new self(
            "Value must be of type `$requiredType`.",
            static::E_INVALID_TYPE
        );
    }

    public static function throwProviderIssue(Throwable $e): void
    {
        throw new self($e->getMessage(), static::E_PROVIDER_ISSUE, $e);
    }
}
