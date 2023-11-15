<?php

declare(strict_types=1);

namespace Paysera\CheckoutSdk\Tests\Validator;

use Mockery as m;
use Paysera\CheckoutSdk\Entity\Order;
use Paysera\CheckoutSdk\Entity\PaymentRedirectRequest;
use Paysera\CheckoutSdk\Entity\PaymentCallbackValidationRequest;
use Paysera\CheckoutSdk\Entity\RequestInterface;
use Paysera\CheckoutSdk\Exception\BaseException;
use Paysera\CheckoutSdk\Exception\InvalidTypeException;
use Paysera\CheckoutSdk\Tests\AbstractCase;
use Paysera\CheckoutSdk\Validator\CountryCodeIso2Validator;
use Paysera\CheckoutSdk\Validator\PaymentRedirectRequestValidator;

class PaymentRedirectRequestValidatorTest extends AbstractCase
{
    protected ?PaymentRedirectRequestValidator $validator = null;

    public function mockeryTestSetUp(): void
    {
        parent::mockeryTestSetUp();

        $this->validator = $this->container->get(PaymentRedirectRequestValidator::class);
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
                ->getMock();

            $this->container->set(CountryCodeIso2Validator::class, $countryValidatorMock);
        }

        $this->container->build(PaymentRedirectRequestValidator::class)->validate($request);
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
                'The entity is compatible for validation.',
            ],
            'incompatibleRequest' => [
                new PaymentCallbackValidationRequest(1, 'pass', 'data'),
                false,
                'The entity is not compatible for validation.',
            ],
        ];
    }
}
