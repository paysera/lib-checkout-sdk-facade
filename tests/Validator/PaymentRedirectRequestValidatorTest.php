<?php

declare(strict_types=1);

namespace Paysera\CheckoutSdk\Tests\Validator;

use Mockery as m;
use Paysera\CheckoutSdk\Entity\Order;
use Paysera\CheckoutSdk\Entity\PaymentRedirectRequest;
use Paysera\CheckoutSdk\Entity\PaymentValidationRequest;
use Paysera\CheckoutSdk\Entity\RequestInterface;
use Paysera\CheckoutSdk\Exception\BaseException;
use Paysera\CheckoutSdk\Exception\InvalidTypeException;
use Paysera\CheckoutSdk\Tests\AbstractCase;
use Paysera\CheckoutSdk\Validator\CountryCodeIso2Validator;
use Paysera\CheckoutSdk\Validator\PaymentRedirectRequestValidator;

class PaymentRedirectRequestValidatorTest extends AbstractCase
{
    /**
     * @dataProvider canValidateRequestsDataProvider
     */
    public function testCanValidate(RequestInterface $request, bool $isCompatible, string $message): void
    {
        $validator = new PaymentRedirectRequestValidator();

        $this->assertEquals($isCompatible, $validator->canValidate($request), $message);
    }

    /**
     * @dataProvider canValidateRequestsDataProvider
     */
    public function testConvert(RequestInterface $request, bool $isCompatible): void
    {
        if ($isCompatible === false) {
            $validator = new PaymentRedirectRequestValidator();

            $this->expectException(InvalidTypeException::class);
            $this->expectExceptionCode(BaseException::E_INVALID_TYPE);
        } else {
            $countryValidator = m::mock('overload:' . CountryCodeIso2Validator::class);
            $countryValidator->shouldReceive('validate')
                ->once()
                ->with('gb');

            $validator = new PaymentRedirectRequestValidator();
        }

        $validator->validate($request);
    }

    public function canValidateRequestsDataProvider(): array
    {
        return [
            'compatibleRequest' => [
                new PaymentRedirectRequest(
                    1,
                    'pass',
                    'test',
                    'test',
                    'test',
                    (new Order(1, 100.0, 'USD'))
                        ->setPaymentCountryCode('gb')
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
