<?php

declare(strict_types=1);

namespace Paysera\CheckoutSdk\Tests\Validator;

use Mockery as m;
use Paysera\CheckoutSdk\Entity\Order;
use Paysera\CheckoutSdk\Entity\PaymentMethodRequest;
use Paysera\CheckoutSdk\Entity\PaymentValidationRequest;
use Paysera\CheckoutSdk\Entity\RequestInterface;
use Paysera\CheckoutSdk\Exception\BaseException;
use Paysera\CheckoutSdk\Exception\InvalidTypeException;
use Paysera\CheckoutSdk\Tests\AbstractCase;
use Paysera\CheckoutSdk\Validator\CountryCodeIso2Validator;
use Paysera\CheckoutSdk\Validator\PaymentMethodRequestValidator;

class PaymentMethodRequestValidatorTest extends AbstractCase
{
    protected ?PaymentMethodRequestValidator $validator = null;

    public function mockeryTestSetUp(): void
    {
        parent::mockeryTestSetUp();

        $this->validator = $this->container->get(PaymentMethodRequestValidator::class);
    }

    /**
     * @dataProvider canValidateRequestsDataProvider
     */
    public function testCanValidate(RequestInterface $request, bool $isCompatible, string $message): void
    {
        $this->assertEquals($isCompatible, $this->validator->canValidate($request), $message);
    }

    /**
     * @dataProvider canValidateRequestsDataProvider
     */
    public function testConvert(RequestInterface $request, bool $isCompatible): void
    {
        if ($isCompatible === false) {
            $this->expectException(InvalidTypeException::class);
            $this->expectExceptionCode(BaseException::E_INVALID_TYPE);
        } else {
            $countryValidatorMock = m::mock(CountryCodeIso2Validator::class)
                ->shouldReceive('validate')
                ->once()
                ->with('gb')
                ->getMock()
                ->shouldReceive('validate')
                ->once()
                ->with('lt')
                ->getMock();

            $this->container->set(CountryCodeIso2Validator::class, $countryValidatorMock);
        }

        $this->container->build(PaymentMethodRequestValidator::class)->validate($request);
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
