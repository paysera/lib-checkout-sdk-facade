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

    public function testAppendInvalidType(): void
    {
        $collection = new PaymentMethodCountryCollection();

        $this->expectException(InvalidTypeException::class);

        $collection->append(m::mock(PaymentMethodGroup::class));
    }

    public function testKey(): void
    {
        $collection = new PaymentMethodCountryCollection();

        $this->assertEquals(0, $collection->key(), 'The default collection key must return.');
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
            static fn (PaymentMethodCountry $paymentMethodCountry) => $paymentMethodCountry->getCode() === 'gb'
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

    public function isCompatibleDataProvider(): array
    {
        return [
            'compatibleItem'   => [PaymentMethodCountry::class, true, 'The entity must be compatible.'],
            'incompatibleItem' => [PaymentMethodGroup::class, false, 'The entity must be not compatible.'],
        ];
    }
}
