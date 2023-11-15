<?php

declare(strict_types=1);

namespace Paysera\CheckoutSdk\Tests\Service;

use Paysera\CheckoutSdk\Entity\Collection\PaymentMethodCountryCollection;
use Paysera\CheckoutSdk\Entity\PaymentMethodCountry;
use Paysera\CheckoutSdk\Service\PaymentMethodCountryManager;
use Paysera\CheckoutSdk\Tests\AbstractCase;

class PaymentMethodCountryManagerTest extends AbstractCase
{
    public function testGetFromCollectionByCode(): void
    {
        $paymentMethodCountry1 = new PaymentMethodCountry('gb');
        $paymentMethodCountry2 = new PaymentMethodCountry('lt');
        $collection = new PaymentMethodCountryCollection([$paymentMethodCountry1, $paymentMethodCountry2]);
        $manager = new PaymentMethodCountryManager();

        $this->assertEquals(
            'gb',
            $manager->getFromCollectionByCode($collection, 'gb')->getCode(),
            'The item country code must be equal to the search code.'
        );
    }

    public function testFilterCollectionByCodes(): void
    {
        $paymentMethodCountry1 = new PaymentMethodCountry('gb');
        $paymentMethodCountry2 = new PaymentMethodCountry('lt');
        $paymentMethodCountry3 = new PaymentMethodCountry('lv');
        $collection = new PaymentMethodCountryCollection([
            $paymentMethodCountry1,
            $paymentMethodCountry2,
            $paymentMethodCountry3,
        ]);
        $manager = new PaymentMethodCountryManager();


        $filteredCollection = $manager->filterCollectionByCodes($collection, ['Lt', 'LV', 'it']);

        $this->assertCount(
            2,
            $filteredCollection,
            'The filtered collection must not be empty.'
        );

        $filteredCollection->rewind();
        $this->assertEquals(
            0,
            $filteredCollection->key(),
            'The filtered collection cursor must be on the first element.'
        );
        $this->assertEquals(
            'lt',
            $filteredCollection->get()->getCode(),
            'The filtered collection item must correspond to the filter condition.'
        );

        $filteredCollection->next();
        $this->assertEquals(
            1,
            $filteredCollection->key(),
            'The filtered collection cursor must be on the second element.'
        );
        $this->assertEquals(
            'lv',
            $filteredCollection->get()->getCode(),
            'The filtered collection item must correspond to the filter condition.'
        );
    }
}
