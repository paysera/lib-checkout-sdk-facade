<?php

declare(strict_types=1);

namespace Paysera\CheckoutSdk\Tests\Entity\Collection;

use Mockery as m;
use Paysera\CheckoutSdk\Entity\Collection\PaymentMethodCountryCollection;
use Paysera\CheckoutSdk\Entity\PaymentMethodCountry;
use Paysera\CheckoutSdk\Entity\PaymentMethodGroup;
use Paysera\CheckoutSdk\Exception\BaseException;
use Paysera\CheckoutSdk\Exception\InvalidTypeException;
use Paysera\CheckoutSdk\Tests\AbstractCase;

class PaymentMethodCountryCollectionTest extends AbstractCase
{
    /**
     * @dataProvider isCompatibleDataProvider
     */
    public function testExchangeArray(string $class, bool $isCompatible): void
    {
        $collection = new PaymentMethodCountryCollection();

        if ($isCompatible === false) {
            $this->expectException(InvalidTypeException::class);
            $this->expectExceptionCode(BaseException::E_INVALID_TYPE);
        }

        $collection->exchangeArray([m::mock($class)]);

        $this->assertCount(
            (int) $isCompatible,
            $collection,
            'The collection must be not empty after the array exchange.'
        );
    }

    /**
     * @dataProvider isCompatibleDataProvider
     */
    public function testIsCompatible(string $class, bool $isCompatible, string $message): void
    {
        $collection = new PaymentMethodCountryCollection();

        $this->assertEquals($isCompatible, $collection->isCompatible(m::mock($class)), $message);
    }

    public function testAppend(): void
    {
        $collection = new PaymentMethodCountryCollection();
        $collection->append(m::mock(PaymentMethodCountry::class));

        $this->assertCount(
            1,
            $collection,
            'The collection must be not empty after the item append.'
        );
    }

    public function testCurrent(): void
    {
        $collection = new PaymentMethodCountryCollection([m::mock(PaymentMethodCountry::class)]);

        foreach ($collection as $item) {
            $this->assertInstanceOf(
                PaymentMethodCountry::class,
                $item,
                'The collection item type is invalid.'
            );
        }
    }

    public function testFilter(): void
    {
        $paymentMethodCountry1 = new PaymentMethodCountry('gb');
        $paymentMethodCountry2 = new PaymentMethodCountry('lt');
        $collection = new PaymentMethodCountryCollection([$paymentMethodCountry1, $paymentMethodCountry2]);

        $filteredCollection = $collection->filter(
            static fn(PaymentMethodCountry $paymentMethodCountry) => $paymentMethodCountry->getCode() === 'gb'
        );

        $this->assertCount(
            1,
            $filteredCollection,
            'The filtered collection must not be empty.'
        );
        $this->assertEquals(
            'gb',
            $filteredCollection->get()->getCode(),
            'The filtered collection item must correspond to the filter condition.'
        );
    }

    public function testFilterByCountryCodes(): void
    {
        $paymentMethodCountry1 = new PaymentMethodCountry('gb');
        $paymentMethodCountry2 = new PaymentMethodCountry('lt');
        $paymentMethodCountry3 = new PaymentMethodCountry('lv');
        $collection = new PaymentMethodCountryCollection([
            $paymentMethodCountry1,
            $paymentMethodCountry2,
            $paymentMethodCountry3
        ]);

        $filteredCollection = $collection->filterByCountryCodes(['Lt', 'LV', 'it']);

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

    public function testGetByCode(): void
    {
        $paymentMethodCountry1 = new PaymentMethodCountry('gb');
        $paymentMethodCountry2 = new PaymentMethodCountry('lt');
        $collection = new PaymentMethodCountryCollection([$paymentMethodCountry1, $paymentMethodCountry2]);

        $this->assertEquals(
            'gb',
            $collection->getByCode('gb')->getCode(),
            'The item country code must be equal to the search code.'
        );
    }

    public function isCompatibleDataProvider(): array
    {
        return [
            'compatibleItem'   => [PaymentMethodCountry::class, true, 'The entity must be compatible.'],
            'incompatibleItem' => [PaymentMethodGroup::class, false, 'The entity must be not compatible.'],
        ];
    }
}
