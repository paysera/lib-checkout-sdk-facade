<?php

declare(strict_types=1);

namespace Paysera\CheckoutSdk\Tests\Entity\Collection;

use Mockery as m;
use Paysera\CheckoutSdk\Entity\Collection\TranslationCollection;
use Paysera\CheckoutSdk\Entity\PaymentMethodGroup;
use Paysera\CheckoutSdk\Entity\Translation;
use Paysera\CheckoutSdk\Exception\CheckoutIntegrationException;
use Paysera\CheckoutSdk\Tests\AbstractCase;

class TranslationCollectionTest extends AbstractCase
{
    /**
     * @dataProvider isCompatibleDataProvider
     */
    public function testExchangeArray(string $class, bool $isCompatible): void
    {
        $collection = new TranslationCollection();

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
        $collection = new TranslationCollection();

        $this->assertEquals($isCompatible, $collection->isCompatible(m::mock($class)), $message);
    }

    public function testAppend(): void
    {
        $collection = new TranslationCollection();
        $collection->append(m::mock(Translation::class));

        $this->assertCount(
            1,
            $collection,
            'The collection must be not empty after the item append.'
        );
    }

    public function testCurrent(): void
    {
        $collection = new TranslationCollection([m::mock(Translation::class)]);

        foreach ($collection as $item) {
            $this->assertInstanceOf(
                Translation::class,
                $item,
                'The collection item type is invalid.'
            );
        }
    }

    public function testFilter(): void
    {
        $translation1 = new Translation('en', 'en text');
        $translation2 = new Translation('lt', 'lt text');
        $collection = new TranslationCollection([$translation1, $translation2]);

        $filteredCollection = $collection->filter(
            static fn(Translation $translation) => $translation->getLanguage() === 'en'
        );

        $this->assertCount(
            1,
            $filteredCollection,
            'The filtered collection must not be empty.'
        );
        $this->assertEquals(
            'en',
            $filteredCollection->current()->getLanguage(),
            'The filtered collection item must correspond to the filter condition.'
        );
    }

    public function testGetByLanguage(): void
    {
        $translation1 = new Translation('en', 'en text');
        $translation2 = new Translation('lt', 'lt text');
        $collection = new TranslationCollection([$translation1, $translation2]);

        $this->assertEquals(
            'en',
            $collection->getByLanguage('en')->getLanguage(),
            'The translation language must be equal to the search language.'
        );
    }

    public function isCompatibleDataProvider(): array
    {
        return [
            'compatibleItem'   => [Translation::class, true, 'The entity must be compatible.'],
            'incompatibleItem' => [PaymentMethodGroup::class, false, 'The entity must be not compatible.'],
        ];
    }
}
