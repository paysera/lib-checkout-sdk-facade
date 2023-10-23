<?php

declare(strict_types=1);

namespace Paysera\CheckoutSdk\Tests\Entity\Collection;

use Mockery as m;
use Paysera\CheckoutSdk\Entity\Collection\PaymentMethodGroupCollection;
use Paysera\CheckoutSdk\Entity\PaymentMethodCountry;
use Paysera\CheckoutSdk\Entity\PaymentMethodGroup;
use Paysera\CheckoutSdk\Exception\CheckoutIntegrationException;
use Paysera\CheckoutSdk\Tests\AbstractCase;

class PaymentMethodGroupCollectionTest extends AbstractCase
{
    /**
     * @dataProvider isCompatibleDataProvider
     */
    public function testExchangeArray(string $class, bool $isCompatible): void
    {
        $collection = new PaymentMethodGroupCollection();

        if ($isCompatible === false) {
            $this->expectException(CheckoutIntegrationException::class);
            $this->expectExceptionCode(CheckoutIntegrationException::E_INVALID_TYPE);
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
        $collection = new PaymentMethodGroupCollection();

        $this->assertEquals($isCompatible, $collection->isCompatible(m::mock($class)), $message);
    }

    public function testAppend(): void
    {
        $collection = new PaymentMethodGroupCollection();
        $collection->append(m::mock(PaymentMethodGroup::class));

        $this->assertCount(
            1,
            $collection,
            'The collection must be not empty after the item append.'
        );
    }

    public function testCurrent(): void
    {
        $collection = new PaymentMethodGroupCollection([m::mock(PaymentMethodGroup::class)]);

        foreach ($collection as $item) {
            $this->assertInstanceOf(
                PaymentMethodGroup::class,
                $item,
                'The collection item type is invalid.'
            );
        }
    }

    public function testFilter(): void
    {
        $paymentMethodGroup1 = new PaymentMethodGroup('1');
        $paymentMethodGroup2 = new PaymentMethodGroup('2');
        $collection = new PaymentMethodGroupCollection([$paymentMethodGroup1, $paymentMethodGroup2]);

        $filteredCollection = $collection->filter(
            static fn(PaymentMethodGroup $paymentMethodGroup) => $paymentMethodGroup->getKey() === '1'
        );

        $this->assertCount(
            1,
            $filteredCollection,
            'The filtered collection must not be empty.'
        );
        $this->assertEquals(
            '1',
            $filteredCollection->current()->getKey(),
            'The filtered collection item must correspond to the filter condition.'
        );
    }

    public function isCompatibleDataProvider(): array
    {
        return [
            'compatibleItem'   => [PaymentMethodGroup::class, true, 'The entity must be compatible.'],
            'incompatibleItem' => [PaymentMethodCountry::class, false, 'The entity must be not compatible.'],
        ];
    }
}
