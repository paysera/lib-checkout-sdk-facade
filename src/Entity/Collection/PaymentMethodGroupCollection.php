<?php

declare(strict_types=1);

namespace Paysera\CheckoutSdk\Entity\Collection;

use Paysera\CheckoutSdk\Entity\PaymentMethodGroup;

/**
 * @method PaymentMethodGroupCollection filter(callable $filterFunction)
 */
class PaymentMethodGroupCollection extends Collection
{
    public function isCompatible(object $item): bool
    {
        return $item instanceof PaymentMethodGroup;
    }

    public function append(PaymentMethodGroup $value): void
    {
        $this->appendToCollection($value);
    }

    public function set($key, PaymentMethodGroup $value): void
    {
        $this->setToCollection($key, $value);
    }

    public function current(): PaymentMethodGroup
    {
        return parent::current();
    }
}
