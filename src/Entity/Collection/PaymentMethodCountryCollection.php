<?php

declare(strict_types=1);

namespace Paysera\CheckoutSdk\Entity\Collection;

use Paysera\CheckoutSdk\Entity\PaymentMethodCountry;

/**
 * @method PaymentMethodCountryCollection filter(callable $filterFunction)
 */
class PaymentMethodCountryCollection extends Collection
{
    public function isCompatible(object $item): bool
    {
        return $item instanceof PaymentMethodCountry;
    }

    public function getByCode(string $code): ?PaymentMethodCountry
    {
        return $this->getByGetter($code, 'getCode');
    }

    public function append(PaymentMethodCountry $value): void
    {
        $this->appendToCollection($value);
    }

    public function set($key, PaymentMethodCountry $value): void
    {
        $this->setToCollection($key, $value);
    }

    public function current(): PaymentMethodCountry
    {
        return parent::current();
    }
}
