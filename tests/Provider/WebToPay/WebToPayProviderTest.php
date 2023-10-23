<?php

declare(strict_types=1);

namespace Paysera\CheckoutSdk\Tests\Provider\WebToPay;

use Mockery as m;
use Paysera\CheckoutSdk\Entity\Order;
use Paysera\CheckoutSdk\Entity\PaymentMethodCountry;
use Paysera\CheckoutSdk\Entity\PaymentMethodRequest;
use Paysera\CheckoutSdk\Entity\PaymentRedirectRequest;
use Paysera\CheckoutSdk\Entity\PaymentValidationRequest;
use Paysera\CheckoutSdk\Entity\PaymentValidationResponse;
use Paysera\CheckoutSdk\Exception\CheckoutIntegrationException;
use Paysera\CheckoutSdk\Provider\WebToPay\Adapter\PaymentMethodCountryAdapter;
use Paysera\CheckoutSdk\Provider\WebToPay\Adapter\PaymentValidationResponseAdapter;
use Paysera\CheckoutSdk\Provider\WebToPay\WebToPayProvider;
use Paysera\CheckoutSdk\Tests\AbstractCase;
use WebToPay;
use WebToPay_PaymentMethodCountry;
use WebToPay_PaymentMethodList;
use WebToPayException;

class WebToPayProviderTest extends AbstractCase
{
    protected const EXCEPTION_MESSAGE = 'Some problems.';

    /** @var PaymentMethodCountryAdapter|null|m\MockInterface  */
    protected ?PaymentMethodCountryAdapter $paymentMethodCountryAdapterMock = null;

    /** @var PaymentValidationResponseAdapter|null|m\MockInterface */
    protected ?PaymentValidationResponseAdapter $paymentValidationResponseAdapterMock = null;

    protected ?WebToPayProvider $webToPayProvider = null;

    public function mockeryTestSetUp(): void
    {
        parent::mockeryTestSetUp();

        $this->paymentMethodCountryAdapterMock = m::mock(PaymentMethodCountryAdapter::class);
        $this->paymentValidationResponseAdapterMock = m::mock(PaymentValidationResponseAdapter::class);

        $this->webToPayProvider = new WebToPayProvider(
            $this->paymentMethodCountryAdapterMock,
            $this->paymentValidationResponseAdapterMock
        );
    }

    public function testGetPaymentMethodCountries(): void
    {
        $methodRequest = new PaymentMethodRequest(
            1,
            'USD',
            'en',
            new Order(1, 100.0, 'USD')
        );

        $providerMethodCountryMock = m::mock(WebToPay_PaymentMethodCountry::class);
        $providerMethodListMock = m::mock(WebToPay_PaymentMethodList::class);
        $providerMethodListMock->expects()
            ->filterForAmount($methodRequest->getOrder()->getAmount(), $methodRequest->getOrder()->getCurrency())
            ->andReturn($providerMethodListMock);
        $providerMethodListMock->expects()
            ->setDefaultLanguage($methodRequest->getLanguage())
            ->andReturn($providerMethodListMock);
        $providerMethodListMock->expects()
            ->getCountries()
            ->andReturn([$providerMethodCountryMock]);

        m::mock('overload:'. WebToPay::class)
            ->expects()
            ->getPaymentMethodList($methodRequest->getProjectId(), $methodRequest->getCurrency())
            ->andReturn($providerMethodListMock);

        $paymentMethodCountry = m::mock(PaymentMethodCountry::class);
        $this->paymentMethodCountryAdapterMock->expects()
            ->convert($providerMethodCountryMock)
            ->andReturn($paymentMethodCountry);

        $collection = $this->webToPayProvider->getPaymentMethodCountries($methodRequest);

        $collection->rewind();
        $this->assertEquals(
            $paymentMethodCountry,
            $collection->current(),
            'The provider must return countries collection.'
        );
    }

    public function testRedirectToPayment(): void
    {
        [$redirectRequest, $providerData] = $this->getPaymentRedirectRequestAndProviderData();

        m::mock('overload:'. WebToPay::class)
            ->expects()
            ->redirectToPayment($providerData, true);

        $this->webToPayProvider->redirectToPayment($redirectRequest);
    }

    public function testValidatePayment(): void
    {
        [$validationRequest, $providerData] = $this->getPaymentValidationRequestAndProviderData();

        m::mock('overload:'. WebToPay::class)
            ->expects()
            ->validateAndParseData(
                $providerData,
                $validationRequest->getProjectId(),
                $validationRequest->getProjectPassword()
            )
            ->andReturn(['some data here...']);

        $validationResponseMock = m::mock(PaymentValidationResponse::class);
        $this->paymentValidationResponseAdapterMock->expects()
            ->convert(['some data here...'])
            ->andReturn($validationResponseMock);

        $validateResponse = $this->webToPayProvider->validatePayment($validationRequest);

        $this->assertEquals(
            $validationResponseMock,
            $validateResponse,
            'The provider must return validation response.'
        );
    }

    /**
     * @dataProvider exceptionsProvider
     */
    public function testMethodsExceptions(string $method, object $request, string $providerMethod): void
    {
        m::mock('overload:'. WebToPay::class)
            ->shouldReceive($providerMethod)
            ->once()
            ->withAnyArgs()
            ->andThrow(new WebToPayException(static::EXCEPTION_MESSAGE));

        $this->expectException(CheckoutIntegrationException::class);
        $this->expectExceptionMessage(static::EXCEPTION_MESSAGE);
        $this->expectExceptionCode(CheckoutIntegrationException::E_PROVIDER_ISSUE);

        $this->webToPayProvider->{$method}($request);
    }

    public function exceptionsProvider(): array
    {
        return [
            [
                'WebToPayProvider method' => 'getPaymentMethodCountries',
                'Request argument' => new PaymentMethodRequest(
                    1,
                    'USD',
                    'en'
                ),
                'WebToPay static method' => 'getPaymentMethodList',
            ],
            [
                'WebToPayProvider method' => 'redirectToPayment',
                'Request argument' => new PaymentRedirectRequest(
                    1,
                    'pass',
                    'acceptUrl',
                    'cancelUrl',
                    'callbackUrl',
                    new Order(1, 100.0, 'USD')
                ),
                'WebToPay static method' => 'redirectToPayment',
            ],
            [
                'WebToPayProvider method' => 'validatePayment',
                'Request argument' => new PaymentValidationRequest(
                    1,
                    'pass',
                    'test data'
                ),
                'WebToPay static method' => 'validateAndParseData',
            ],
        ];
    }

    /**
     * @return array{0: PaymentRedirectRequest, 1: array}
     */
    protected function getPaymentRedirectRequestAndProviderData(): array
    {
        $orderRequest = (new Order(
            1,
            100.0,
            'USD'
        ))->setPaymentFirstName('John')
            ->setPaymentLastName('Doe')
            ->setPaymentEmail('john.doe@paysera.net')
            ->setPaymentStreet('Sun str. 1')
            ->setPaymentCity('London')
            ->setPaymentZip('100')
            ->setPaymentCountryCode('gb');
        $redirectRequest = (new PaymentRedirectRequest(
            1,
            'pass',
            'acceptUrl',
            'cancelUrl',
            'callbackUrl',
            $orderRequest
        ))
            ->setPayment('card')
            ->setTest(true)
            ->setCountry('gb');

        $providerData = [
            'projectid' => 1,
            'sign_password' => 'pass',
            'orderid' => 1,
            'amount' => 100,
            'currency' => 'USD',
            'accepturl' => 'acceptUrl',
            'cancelurl' => 'cancelUrl',
            'callbackurl' => 'callbackUrl',
            'payment' => 'card',
            'country' => 'gb',
            'p_firstname' => 'John',
            'p_lastname' => 'Doe',
            'p_email' => 'john.doe@paysera.net',
            'p_street' => 'Sun str. 1',
            'p_city' => 'London',
            'p_zip' => '100',
            'p_countrycode' => 'gb',
            'test' => 1,
        ];

        return [$redirectRequest, $providerData];
    }

    /**
     * @return array{0: PaymentValidationRequest, 1: array}
     */
    protected function getPaymentValidationRequestAndProviderData(): array
    {
        $validationRequest = (new PaymentValidationRequest(
            1,
            'pass',
            'test data'
        ))
            ->setSs1('test ss1')
            ->setSs2('test ss2')
            ->setType('test type')
            ->setTo('someone')
            ->setFrom('me')
            ->setSms([1, 2]);

        $providerData = [
            'data' => 'test data',
            'ss1' => 'test ss1',
            'ss2' => 'test ss2',
            'type' => 'test type',
            'to' => 'someone',
            'from' => 'me',
            'sms' => [1, 2],
        ];

        return [$validationRequest, $providerData];
    }
}
