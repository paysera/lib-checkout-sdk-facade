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
        return $this->filter(
            static fn (PaymentMethodCountry $paymentMethodCountry) => $paymentMethodCountry->getCode()
            === $code
        )->current();
    }

    public function filterByCountryCodes(array $selectedCountries): self
    {
        $selectedCountriesLowercase = array_map('strtolower', $selectedCountries);
        return $this->filter(
            static fn (PaymentMethodCountry $country) => in_array(
                strtolower($country->getCode()),
                $selectedCountriesLowercase,
                true
            )
        );
    }

    public function append(PaymentMethodCountry $value): void
    {
        $this->appendToCollection($value);
    }

    public function current(): ?PaymentMethodCountry
    {
        return parent::current();
    }

    protected function getItemType(): string
    {
        return PaymentMethodCountry::class;
    }
}
