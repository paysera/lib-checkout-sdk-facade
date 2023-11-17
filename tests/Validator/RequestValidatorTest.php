<?php

declare(strict_types=1);

namespace Paysera\CheckoutSdk\Tests\Validator;

use Mockery as m;
use Paysera\CheckoutSdk\Entity\Request\PaymentMethodsRequest;
use Paysera\CheckoutSdk\Tests\AbstractCase;
use Paysera\CheckoutSdk\Validator\PaymentMethodsRequestValidator;
use Paysera\CheckoutSdk\Validator\PaymentRedirectRequestValidator;
use Paysera\CheckoutSdk\Validator\RequestValidator;

class RequestValidatorTest extends AbstractCase
{
    public function testValidate(): void
    {
        $paymentMethodRequestValidator = m::mock(PaymentMethodsRequestValidator::class);
        $paymentMethodRequestValidator->shouldReceive('canValidate')
            ->once()
            ->withAnyArgs()
            ->andReturn(true);
        $paymentMethodRequestValidator->shouldReceive('validate')
            ->once()
            ->withAnyArgs();

        $paymentRedirectRequestValidator = m::mock(PaymentRedirectRequestValidator::class);
        $paymentRedirectRequestValidator->shouldReceive('canValidate')
            ->once()
            ->withAnyArgs()
            ->andReturn(false);
        $paymentRedirectRequestValidator->shouldReceive('validate')
            ->never()
            ->withAnyArgs();

        $requestValidator = new RequestValidator($paymentMethodRequestValidator, $paymentRedirectRequestValidator);

        $requestValidator->validate(m::mock(PaymentMethodsRequest::class));
    }
}
