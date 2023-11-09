<?php

namespace Paysera\CheckoutSdk\Tests\Validator;

use Mockery as m;
use Paysera\CheckoutSdk\Entity\PaymentMethodRequest;
use Paysera\CheckoutSdk\Tests\AbstractCase;
use Paysera\CheckoutSdk\Validator\PaymentMethodRequestValidator;
use Paysera\CheckoutSdk\Validator\PaymentRedirectRequestValidator;
use Paysera\CheckoutSdk\Validator\RequestValidator;

class RequestValidatorTest extends AbstractCase
{
    public function testValidate(): void
    {
        $paymentMethodRequestValidator = m::mock(PaymentMethodRequestValidator::class);
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

        $requestValidator->validate(m::mock(PaymentMethodRequest::class));
    }
}
