<?php

declare(strict_types=1);

namespace Paysera\CheckoutSdk\Tests\Validator;

use Mockery as m;
use Paysera\CheckoutSdk\Entity\Order;
use Paysera\CheckoutSdk\Entity\Request\PaymentMethodsRequest;
use Paysera\CheckoutSdk\Entity\Request\PaymentCallbackValidationRequest;
use Paysera\CheckoutSdk\Entity\RequestInterface;
use Paysera\CheckoutSdk\Exception\BaseException;
use Paysera\CheckoutSdk\Exception\InvalidTypeException;
use Paysera\CheckoutSdk\Exception\ValidationException;
use Paysera\CheckoutSdk\Tests\AbstractCase;
use Paysera\CheckoutSdk\Validator\CountryCodeIso2Validator;
use Paysera\CheckoutSdk\Validator\PaymentMethodsRequestValidator;

class PaymentMethodsRequestValidatorTest extends AbstractCase
{
    protected ?PaymentMethodsRequestValidator $validator = null;

    public function mockeryTestSetUp(): void
    {
        parent::mockeryTestSetUp();

        $this->validator = $this->container->get(PaymentMethodsRequestValidator::class);
    }

    /**
     * @dataProvider canValidateRequestsDataProvider
     */
    public function testCanValidate(RequestInterface $request, bool $isCompatible, string $message): void
    {
        $this->assertEquals($isCompatible, $this->validator->canValidate($request), $message);
    }

    /**
     * @dataProvider validateRequestsDataProvider
     */
    public function testValidate(RequestInterface $request): void
    {
        $this->expectException(ValidationException::class);

        $this->container->build(PaymentMethodsRequestValidator::class)->validate($request);
    }

    public function canValidateRequestsDataProvider(): array
    {
        return [
            'compatibleRequest' => [
                new PaymentMethodsRequest(1, 100, 'USD'),
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

    public function validateRequestsDataProvider(): array
    {
        return [
            'lessThreeCharInCurrency' => [
                new PaymentMethodsRequest(1, 100, 'US'),
            ],
            'moreThreeCharInCurrency' => [
                new PaymentMethodsRequest(1, 100, 'USDD'),
            ],
        ];
    }
}
