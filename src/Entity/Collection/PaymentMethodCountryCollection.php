<?php

declare(strict_types=1);

namespace Paysera\CheckoutSdk\Entity\Collection;

use Paysera\CheckoutSdk\Entity\PaymentMethodCountry;

/**
 * @template PaymentMethodCountry
 * @extends Collection<PaymentMethodCountry>
 *
 * @method PaymentMethodCountryCollection<PaymentMethodCountry> filter(callable $filterFunction)
 * @method void append(PaymentMethodCountry $value)
 * @method PaymentMethodCountry|null get(int $index = null)
 */
class PaymentMethodCountryCollection extends Collection
{
    public function isCompatible(object $item): bool
    {
        return $item instanceof PaymentMethodCountry;
    }

    public function getByCode(string $code): ?PaymentMethodCountry
    {
        return $this->filter(static fn (PaymentMethodCountry $country) => $country->getCode() === $code)->get();
    }

    /**
     * @param array<int,string> $selectedCountries
     * @return PaymentMethodCountryCollection<PaymentMethodCountry>
     */
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

    public function current(): PaymentMethodCountry
    {
        return parent::current();
    }

    protected function getItemType(): string
    {
        return PaymentMethodCountry::class;
    }
}
