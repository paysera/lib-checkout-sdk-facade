<?php

declare(strict_types=1);

namespace Paysera\CheckoutSdk\Tests\Validator;

use Mockery as m;
use Paysera\CheckoutSdk\Entity\Order;
use Paysera\CheckoutSdk\Entity\PaymentMethodRequest;
use Paysera\CheckoutSdk\Entity\PaymentValidationRequest;
use Paysera\CheckoutSdk\Entity\RequestInterface;
use Paysera\CheckoutSdk\Exception\CheckoutIntegrationException;
use Paysera\CheckoutSdk\Tests\AbstractCase;
use Paysera\CheckoutSdk\Validator\CountryCodeIso2Validator;
use Paysera\CheckoutSdk\Validator\PaymentMethodRequestValidator;

class PaymentMethodRequestValidatorTest extends AbstractCase
{
    /**
     * @dataProvider canValidateRequestsDataProvider
     */
    public function testCanValidate(RequestInterface $request, bool $isCompatible, string $message): void
    {
        $validator = new PaymentMethodRequestValidator();

        $this->assertEquals($isCompatible, $validator->canValidate($request), $message);
    }

    /**
     * @dataProvider canValidateRequestsDataProvider
     */
    public function testConvert(RequestInterface $request, bool $isCompatible): void
    {
        if ($isCompatible === false) {
            $validator = new PaymentMethodRequestValidator();

            $this->expectException(CheckoutIntegrationException::class);
            $this->expectExceptionCode(CheckoutIntegrationException::E_INVALID_TYPE);
        } else {
            $countryValidator = m::mock('overload:' . CountryCodeIso2Validator::class);
            $countryValidator->shouldReceive('validate')
                ->once()
                ->with('gb');
            $countryValidator->shouldReceive('validate')
                ->once()
                ->with('lt');

            $validator = new PaymentMethodRequestValidator();
        }

        $validator->validate($request);
    }

    public function canValidateRequestsDataProvider(): array
    {
        return [
            'compatibleRequest' => [
                new PaymentMethodRequest(
                    1,
                    'en',
                    new Order(1, 100.0, 'USD'),
                    ['gb', 'lt']
                ),
                true,
                'The entity is compatible for validation.'
            ],
            'incompatibleRequest' => [
                new PaymentValidationRequest(1, 'pass', 'data'),
                false,
                'The entity is not compatible for validation.'
            ],
        ];
    }
}
