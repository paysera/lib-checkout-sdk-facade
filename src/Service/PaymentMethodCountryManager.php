<?php

namespace Paysera\CheckoutSdk\Service;

use Paysera\CheckoutSdk\Entity\Collection\PaymentMethodCountryCollection;
use Paysera\CheckoutSdk\Entity\PaymentMethodCountry;

class PaymentMethodCountryManager
{
    /**
     * @param PaymentMethodCountryCollection<PaymentMethodCountry> $collection
     * @param string $code
     * @return ?PaymentMethodCountry
     */
    public function getFromCollectionByCode(
        PaymentMethodCountryCollection $collection,
        string $code
    ): ?PaymentMethodCountry {
        return $collection->filter(static fn (PaymentMethodCountry $country) => $country->getCode() === $code)->get();
    }

    /**
     * Returns new filtered collection
     *
     * @return PaymentMethodCountryCollection<PaymentMethodCountry>
     * @param PaymentMethodCountryCollection<PaymentMethodCountry> $collection
     * @param array $selectedCountries
     */
    public function filterCollectionByCodes(
        PaymentMethodCountryCollection $collection,
        array $selectedCountries
    ): PaymentMethodCountryCollection {
        $selectedCountriesLowercase = array_map('strtolower', $selectedCountries);
        return $collection->filter(
            static fn (PaymentMethodCountry $country) => in_array(
                strtolower($country->getCode()),
                $selectedCountriesLowercase,
                true
            )
        );
    }
}
