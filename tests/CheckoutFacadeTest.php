<?php

declare(strict_types=1);

namespace Paysera\CheckoutSdk\Tests;

use Mockery as m;
use Paysera\CheckoutSdk\CheckoutFacade;
use Paysera\CheckoutSdk\Entity\Collection\PaymentMethodCountryCollection;
use Paysera\CheckoutSdk\Entity\Order;
use Paysera\CheckoutSdk\Entity\PaymentMethodCountry;
use Paysera\CheckoutSdk\Entity\Request\PaymentMethodsRequest;
use Paysera\CheckoutSdk\Entity\Request\PaymentRedirectRequest;
use Paysera\CheckoutSdk\Entity\Request\PaymentCallbackValidationRequest;
use Paysera\CheckoutSdk\Entity\PaymentCallbackValidationResponse;
use Paysera\CheckoutSdk\Provider\ProviderInterface;
use Paysera\CheckoutSdk\Service\PaymentStatus;
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
        $request = m::mock(PaymentMethodsRequest::class);

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

        $this->providerMock->shouldReceive('getPaymentMethods')
            ->once()
            ->with($request)
            ->andReturn($collection);

        $resultCollection = $this->facade->getPaymentMethods($request);

        $this->assertCount(
            3,
            $resultCollection,
            'The facade must return countries collection.'
        );
    }

    public function testGetPaymentRedirect(): void
    {
        $request = m::mock(PaymentRedirectRequest::class);

        $this->requestValidatorMock->shouldReceive('validate')
            ->once()
            ->with($request);

        $this->providerMock->shouldReceive('getPaymentRedirect')
            ->once()
            ->with($request);

        $this->facade->getPaymentRedirect($request);
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

    /**
     * @dataProvider isMerchantOrderPaidDataProvider
     */
    public function testIsMerchantOrderPaid(
        bool $expected,
        Order $merchantOrder,
        PaymentCallbackValidationResponse $callbackResponse
    ): void {
        $this->assertEquals(
            $expected,
            $this->facade->isMerchantOrderPaid($callbackResponse, $merchantOrder),
            'The values must be equal.'
        );
    }

    public function isMerchantOrderPaidDataProvider(): array
    {
        return [
            'response_payment_status_is_not_executed' => [
                false,
                new Order(1, 1, 'EUR'),
                new PaymentCallbackValidationResponse(
                    1,
                    new Order(1, 1, 'EUR'),
                    PaymentStatus::NOT_EXECUTED
                ),
            ],
            'response_payment_status_is_success' => [
                true,
                new Order(1, 1, 'EUR'),
                new PaymentCallbackValidationResponse(
                    1,
                    new Order(1, 1, 'EUR'),
                    PaymentStatus::SUCCESS
                ),
            ],
            'response_payment_status_is_accepted' => [
                false,
                new Order(1, 1, 'EUR'),
                new PaymentCallbackValidationResponse(
                    1,
                    new Order(1, 1, 'EUR'),
                    PaymentStatus::ACCEPTED
                ),
            ],
            'response_payment_status_is_additional_information' => [
                false,
                new Order(1, 1, 'EUR'),
                new PaymentCallbackValidationResponse(
                    1,
                    new Order(1, 1, 'EUR'),
                    PaymentStatus::ADDITIONAL_INFORMATION
                ),
            ],
            'response_payment_status_is_executed_without_confirmation' => [
                false,
                new Order(1, 1, 'EUR'),
                new PaymentCallbackValidationResponse(
                    1,
                    new Order(1, 1, 'EUR'),
                    PaymentStatus::EXECUTED_WITHOUT_CONFIRMATION
                ),
            ],
            'merchant_order_is_equal_to_response_payment_data' => [
                true,
                new Order(1, 1, 'EUR'),
                (new PaymentCallbackValidationResponse(
                    1,
                    new Order(1, 10, 'USD'),
                    PaymentStatus::SUCCESS
                ))->setPaymentAmount(1)->setPaymentCurrency('EUR'),
            ],
            'merchant_order_is_not_equal_to_response_order_data' => [
                false,
                new Order(1, 1, 'EUR'),
                new PaymentCallbackValidationResponse(
                    1,
                    new Order(1, 10, 'USD'),
                    PaymentStatus::SUCCESS
                ),
            ],
        ];
    }
}
