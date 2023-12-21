<?php

declare(strict_types=1);

namespace Paysera\CheckoutSdk\Tests\Entity\Collection;

use Mockery as m;
use Paysera\CheckoutSdk\Entity\Collection\PaymentMethodCollection;
use Paysera\CheckoutSdk\Entity\PaymentMethod;
use Paysera\CheckoutSdk\Entity\PaymentMethodGroup;
use Paysera\CheckoutSdk\Exception\BaseException;
use Paysera\CheckoutSdk\Exception\InvalidTypeException;
use Paysera\CheckoutSdk\Tests\AbstractCase;

class PaymentMethodCollectionTest extends AbstractCase
{
    /**
     * @dataProvider isCompatibleDataProvider
     */
    public function testExchangeArray(string $class, bool $isCompatible): void
    {
        $collection = new PaymentMethodCollection();

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
        $collection = new PaymentMethodCollection();

        $this->assertEquals($isCompatible, $collection->isCompatible(m::mock($class)), $message);
    }

    public function testAppend(): void
    {
        $collection = new PaymentMethodCollection();
        $collection->append(m::mock(PaymentMethod::class));

        $this->assertCount(
            1,
            $collection,
            'The collection must be not empty after the item append.'
        );
    }

    public function testAppendInvalidType(): void
    {
        $collection = new PaymentMethodCollection();

        $this->expectException(InvalidTypeException::class);

        $collection->append(m::mock(PaymentMethodGroup::class));
    }

    public function testKey(): void
    {
        $collection = new PaymentMethodCollection();

        $this->assertEquals(0, $collection->key(), 'The default collection key must return.');
    }

    public function testCurrent(): void
    {
        $collection = new PaymentMethodCollection([m::mock(PaymentMethod::class)]);

        foreach ($collection as $item) {
            $this->assertInstanceOf(
                PaymentMethod::class,
                $item,
                'The collection item type is invalid.'
            );
        }
    }

    public function testFilter(): void
    {
        $paymentMethod1 = new PaymentMethod('1');
        $paymentMethod2 = new PaymentMethod('2');
        $collection = new PaymentMethodCollection([$paymentMethod1, $paymentMethod2]);

        $filteredCollection = $collection->filter(
            static fn (PaymentMethod $paymentMethod) => $paymentMethod->getKey() === '1'
        );

        $this->assertCount(
            1,
            $filteredCollection,
            'The filtered collection must not be empty.'
        );
        $this->assertEquals(
            '1',
            $filteredCollection->get()->getKey(),
            'The filtered collection item must correspond to the filter condition.'
        );
    }

    public function isCompatibleDataProvider(): array
    {
        return [
            'compatibleItem'   => [PaymentMethod::class, true, 'The entity must be compatible.'],
            'incompatibleItem' => [PaymentMethodGroup::class, false, 'The entity must be not compatible.'],
        ];
    }
}
