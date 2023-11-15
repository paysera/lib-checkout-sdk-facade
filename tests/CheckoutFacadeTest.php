<?php

declare(strict_types=1);

namespace Paysera\CheckoutSdk\Tests;

use Mockery as m;
use Paysera\CheckoutSdk\CheckoutFacade;
use Paysera\CheckoutSdk\Entity\Collection\PaymentMethodCountryCollection;
use Paysera\CheckoutSdk\Entity\PaymentMethodCountry;
use Paysera\CheckoutSdk\Entity\PaymentMethodRequest;
use Paysera\CheckoutSdk\Entity\PaymentRedirectRequest;
use Paysera\CheckoutSdk\Entity\PaymentCallbackValidationRequest;
use Paysera\CheckoutSdk\Entity\PaymentCallbackValidationResponse;
use Paysera\CheckoutSdk\Provider\ProviderInterface;
use Paysera\CheckoutSdk\Validator\RequestValidator;

class CheckoutFacadeTest extends AbstractCase
{
    protected const EXCEPTION_MESSAGE = 'Some problems.';
    /**
     * @var RequestValidator|null|m\MockInterface
     */
    protected ?RequestValidator $requestValidatorMock = null;
    /**
     * @var ProviderInterface|null|m\MockInterface
     */
    protected ?ProviderInterface $providerMock = null;

    protected ?CheckoutFacade $facade = null;

    public function mockeryTestSetUp(): void
    {
        parent::mockeryTestSetUp();

        $this->requestValidatorMock = m::mock(RequestValidator::class);
        $this->providerMock = m::mock(ProviderInterface::class);

        $this->facade = new CheckoutFacade(
            $this->providerMock,
            $this->requestValidatorMock
        );
    }

    public function testGetPaymentMethodCountries(): void
    {
        $request = m::mock(PaymentMethodRequest::class);
        $request->shouldReceive('getSelectedCountries')
            ->times(2)
            ->andReturn(['gb']);

        $paymentMethodCountry1 = new PaymentMethodCountry('gb');
        $paymentMethodCountry2 = new PaymentMethodCountry('lt');
        $paymentMethodCountry3 = new PaymentMethodCountry('lv');
        $collection = new PaymentMethodCountryCollection([
            $paymentMethodCountry1,
            $paymentMethodCountry2,
            $paymentMethodCountry3,
        ]);

        $this->requestValidatorMock->shouldReceive('validate')
            ->once()
            ->with($request);

        $this->providerMock->shouldReceive('getPaymentMethodCountries')
            ->once()
            ->with($request)
            ->andReturn($collection);

        $resultCollection = $this->facade->getPaymentMethodCountries($request);

        $this->assertCount(
            1,
            $resultCollection,
            'The facade must return filtered country collection with single item.'
        );
        $this->assertEquals(
            $paymentMethodCountry1,
            $resultCollection->get(),
            'The facade must return filtered country collection.'
        );
    }

    public function testRedirectToPayment(): void
    {
        $request = m::mock(PaymentRedirectRequest::class);

        $this->requestValidatorMock->shouldReceive('validate')
            ->once()
            ->with($request);

        $this->providerMock->shouldReceive('redirectToPayment')
            ->once()
            ->with($request);

        $this->facade->redirectToPayment($request);
    }

    public function testValidatePayment(): void
    {
        $request = m::mock(PaymentCallbackValidationRequest::class);
        $response = m::mock(PaymentCallbackValidationResponse::class);

        $this->requestValidatorMock->shouldReceive('validate')
            ->once()
            ->with($request);

        $this->providerMock->shouldReceive('getPaymentCallbackValidatedData')
            ->once()
            ->with($request)
            ->andReturn($response);

        $this->assertEquals(
            $response,
            $this->facade->getPaymentCallbackValidatedData($request),
            'The facade must return validation response.'
        );
    }
}
