<?php

declare(strict_types=1);

namespace Paysera\CheckoutSdk\Exception;

use Exception;
use Throwable;

class CheckoutIntegrationException extends Exception
{
    public const E_VALIDATION = 1;
    public const E_INVALID_TYPE = 2;
    public const E_CURRENCIES_MISMATCH = 3;
    public const E_PROVIDER_ISSUE = 4;

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

    public static function throwCurrenciesMismatch(string $givenCurrency, string $availableCurrency): void
    {
        throw new self(
            "Currencies do not match."
            . " You have to get payment types for the currency you are checking."
            . " Given currency: `$givenCurrency`, available currency: `$availableCurrency`.",
            static::E_CURRENCIES_MISMATCH
        );
    }

    public static function throwProviderIssue(Throwable $e): void
    {
        throw new self($e->getMessage(), static::E_PROVIDER_ISSUE, $e);
    }
}
