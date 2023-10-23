<?php

declare(strict_types=1);

namespace Paysera\CheckoutSdk\Tests;

use Mockery as m;
use Paysera\CheckoutSdk\CheckoutFacade;
use Paysera\CheckoutSdk\Entity\Collection\PaymentMethodCountryCollection;
use Paysera\CheckoutSdk\Entity\Collection\RequestValidatorCollection;
use Paysera\CheckoutSdk\Entity\PaymentMethodRequest;
use Paysera\CheckoutSdk\Entity\PaymentRedirectRequest;
use Paysera\CheckoutSdk\Entity\PaymentValidationRequest;
use Paysera\CheckoutSdk\Entity\PaymentValidationResponse;
use Paysera\CheckoutSdk\Provider\ProviderInterface;

class CheckoutFacadeTest extends AbstractCase
{
    protected const EXCEPTION_MESSAGE = 'Some problems.';
    /**
     * @var RequestValidatorCollection|null|m\MockInterface
     */
    protected ?RequestValidatorCollection $requestValidatorCollectionMock = null;
    /**
     * @var ProviderInterface|null|m\MockInterface
     */
    protected ?ProviderInterface $providerMock = null;

    protected ?CheckoutFacade $facade = null;

    public function mockeryTestSetUp(): void
    {
        parent::mockeryTestSetUp();

        $this->requestValidatorCollectionMock = m::mock(RequestValidatorCollection::class);
        $this->providerMock = m::mock(ProviderInterface::class);

        $this->facade = new CheckoutFacade(
            $this->providerMock,
            $this->requestValidatorCollectionMock
        );
    }

    public function testGetPaymentMethodCountries(): void
    {
        $request = m::mock(PaymentMethodRequest::class);
        $request->shouldReceive('getSelectedCountries')
            ->times(2)
            ->andReturn(['gb']);

        $collection = m::mock(PaymentMethodCountryCollection::class);
        $collection->shouldReceive('filterByCountryCodes')
            ->once()
            ->with(['gb'])
            ->andReturn($collection);

        $this->requestValidatorCollectionMock->shouldReceive('validate')
            ->once()
            ->with($request);

        $this->providerMock->shouldReceive('getPaymentMethodCountries')
            ->once()
            ->with($request)
            ->andReturn($collection);

        $this->assertEquals(
            $collection,
            $this->facade->getPaymentMethodCountries($request),
            'The facade must return countries collection.'
        );
    }

    public function testRedirectToPayment(): void
    {
        $request = m::mock(PaymentRedirectRequest::class);

        $this->requestValidatorCollectionMock->shouldReceive('validate')
            ->once()
            ->with($request);

        $this->providerMock->shouldReceive('redirectToPayment')
            ->once()
            ->with($request);

        $this->facade->redirectToPayment($request);
    }

    public function testValidatePayment(): void
    {
        $request = m::mock(PaymentValidationRequest::class);
        $response = m::mock(PaymentValidationResponse::class);

        $this->requestValidatorCollectionMock->shouldReceive('validate')
            ->once()
            ->with($request);

        $this->providerMock->shouldReceive('validatePayment')
            ->once()
            ->with($request)
            ->andReturn($response);

        $this->assertEquals(
            $response,
            $this->facade->validatePayment($request),
            'The facade must return validation response.'
        );
    }
}
