<?php

declare(strict_types=1);

namespace Paysera\CheckoutSdk\Entity\Collection;

use Paysera\CheckoutSdk\Entity\PaymentMethod;

/**
 * @method PaymentMethodCollection filter(callable $filterFunction)
 */
class PaymentMethodCollection extends Collection
{
    public function isCompatible(object $item): bool
    {
        return $item instanceof PaymentMethod;
    }

    public function append(PaymentMethod $value): void
    {
        $this->appendToCollection($value);
    }

    public function current(): ?PaymentMethod
    {
        return parent::current();
    }

    protected function getItemType(): string
    {
        return PaymentMethod::class;
    }
}
