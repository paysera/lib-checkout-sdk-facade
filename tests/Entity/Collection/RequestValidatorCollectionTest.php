<?php

declare(strict_types=1);

namespace Paysera\CheckoutSdk\Tests\Entity\Collection;

use Mockery as m;
use Paysera\CheckoutSdk\Entity\Collection\RequestValidatorCollection;
use Paysera\CheckoutSdk\Entity\PaymentMethodRequest;
use Paysera\CheckoutSdk\Entity\PaymentCallbackValidationRequest;
use Paysera\CheckoutSdk\Exception\BaseException;
use Paysera\CheckoutSdk\Exception\InvalidTypeException;
use Paysera\CheckoutSdk\Tests\AbstractCase;
use Paysera\CheckoutSdk\Validator\CountryCodeIso2Validator;
use Paysera\CheckoutSdk\Validator\PaymentMethodRequestValidator;
use Paysera\CheckoutSdk\Validator\PaymentRedirectRequestValidator;
use Paysera\CheckoutSdk\Validator\RequestValidatorInterface;

class RequestValidatorCollectionTest extends AbstractCase
{
    /**
     * @dataProvider isCompatibleDataProvider
     */
    public function testExchangeArray(string $class, bool $isCompatible): void
    {
        $collection = new RequestValidatorCollection();

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
        $collection = new RequestValidatorCollection();

        $this->assertEquals($isCompatible, $collection->isCompatible(m::mock($class)), $message);
    }

    public function testCurrent(): void
    {
        $collection = new RequestValidatorCollection([m::mock(PaymentMethodRequestValidator::class)]);

        foreach ($collection as $item) {
            $this->assertInstanceOf(
                PaymentMethodRequestValidator::class,
                $item,
                'The collection item type is invalid.'
            );
        }
    }

    public function testFilter(): void
    {
        $validator1 = $this->container->get(PaymentMethodRequestValidator::class);
        $validator2 = $this->container->get(PaymentRedirectRequestValidator::class);
        $collection = new RequestValidatorCollection([$validator1, $validator2]);

        $filteredCollection = $collection->filter(
            static fn (RequestValidatorInterface $validator) => get_class($validator) === PaymentMethodRequestValidator::class
        );

        $this->assertCount(
            1,
            $filteredCollection,
            'The filtered collection must not be empty.'
        );
        $this->assertInstanceOf(
            PaymentMethodRequestValidator::class,
            $filteredCollection->get(),
            'The filtered collection item must correspond to the filter condition.'
        );
    }

    public function isCompatibleDataProvider(): array
    {
        return [
            'compatibleItem'   => [
                PaymentRedirectRequestValidator::class,
                true,
                'The validator must be compatible for collection.',
            ],
            'incompatibleItem' => [
                CountryCodeIso2Validator::class,
                false,
                'The entity must be not compatible for collection.',
            ],
        ];
    }
}
