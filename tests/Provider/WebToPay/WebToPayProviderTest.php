<?php

declare(strict_types=1);

namespace Paysera\CheckoutSdk\Tests\Provider\WebToPay;

use Mockery as m;
use Paysera\CheckoutSdk\Entity\Order;
use Paysera\CheckoutSdk\Entity\PaymentMethodCountry;
use Paysera\CheckoutSdk\Entity\Request\PaymentMethodsRequest;
use Paysera\CheckoutSdk\Entity\Request\PaymentRedirectRequest;
use Paysera\CheckoutSdk\Entity\Request\PaymentCallbackValidationRequest;
use Paysera\CheckoutSdk\Entity\PaymentCallbackValidationResponse;
use Paysera\CheckoutSdk\Entity\PaymentRedirectResponse;
use Paysera\CheckoutSdk\Exception\BaseException;
use Paysera\CheckoutSdk\Exception\ProviderException;
use Paysera\CheckoutSdk\Provider\WebToPay\Adapter\PaymentCallbackValidationRequestNormalizer;
use Paysera\CheckoutSdk\Provider\WebToPay\Adapter\PaymentMethodCountryAdapter;
use Paysera\CheckoutSdk\Provider\WebToPay\Adapter\PaymentRedirectRequestNormalizer;
use Paysera\CheckoutSdk\Provider\WebToPay\Adapter\PaymentValidationResponseNormalizer;
use Paysera\CheckoutSdk\Provider\WebToPay\Helper\RedirectToPaymentHelper;
use Paysera\CheckoutSdk\Provider\WebToPay\WebToPayProvider;
use Paysera\CheckoutSdk\Tests\AbstractCase;
use WebToPay;
use WebToPay_PaymentMethodCountry;
use WebToPay_PaymentMethodList;
use WebToPayException;

/**
 * @runTestsInSeparateProcesses
 * @preserveGlobalState disabled
 */
class WebToPayProviderTest extends AbstractCase
{
    /** @var PaymentMethodCountryAdapter|null|m\MockInterface  */
    protected ?PaymentMethodCountryAdapter $paymentMethodCountryAdapterMock = null;

    /** @var PaymentValidationResponseNormalizer|null|m\MockInterface */
    protected ?PaymentValidationResponseNormalizer $paymentValidationResponseNormalizer = null;
    /** @var RedirectToPaymentHelper|null|m\MockInterface  */
    protected ?RedirectToPaymentHelper $redirectResponseHelper = null;

    protected ?WebToPayProvider $webToPayProvider = null;

    public function mockeryTestSetUp(): void
    {
        parent::mockeryTestSetUp();

        $this->paymentMethodCountryAdapterMock = m::mock(PaymentMethodCountryAdapter::class);
        $this->paymentValidationResponseNormalizer = m::mock(PaymentValidationResponseNormalizer::class);
        $this->redirectResponseHelper = m::mock(RedirectToPaymentHelper::class);

        $this->webToPayProvider = new WebToPayProvider(
            $this->paymentMethodCountryAdapterMock,
            $this->paymentValidationResponseNormalizer,
            new PaymentRedirectRequestNormalizer(),
            new PaymentCallbackValidationRequestNormalizer(),
            $this->redirectResponseHelper
        );
    }

    public function testGetPaymentMethodCountries(): void
    {
        $methodRequest = new PaymentMethodsRequest(
            1,
            100,
            'USD'
        );

        $providerMethodCountryMock = m::mock(WebToPay_PaymentMethodCountry::class);
        $providerMethodListMock = m::mock(WebToPay_PaymentMethodList::class);
        $providerMethodListMock->expects()
            ->getCountries()
            ->andReturn([$providerMethodCountryMock]);

        m::mock('overload:'. WebToPay::class)
            ->expects()
            ->getPaymentMethodList(
                $methodRequest->getProjectId(),
                $methodRequest->getAmount(),
                $methodRequest->getCurrency()
            )
            ->andReturn($providerMethodListMock);

        $paymentMethodCountry = m::mock(PaymentMethodCountry::class);
        $this->paymentMethodCountryAdapterMock->expects()
            ->convert($providerMethodCountryMock)
            ->andReturn($paymentMethodCountry);

        $collection = $this->webToPayProvider->getPaymentMethodCountries($methodRequest);

        $collection->rewind();
        $this->assertEquals(
            $paymentMethodCountry,
            $collection->get(),
            'The provider must return countries collection.'
        );
    }

    public function testGetPaymentRedirectWithHeader(): void
    {
        [$redirectRequest, $providerData] = $this->getPaymentRedirectRequestAndProviderData();

        m::mock('overload:'. WebToPay::class)
            ->expects()
            ->redirectToPayment($providerData, false);

        $this->redirectResponseHelper->shouldReceive('catchOutputBuffer')
            ->shouldReceive('getResponseHeaders')
            ->andReturn(['Location: http://example.paysera.test'])
            ->shouldReceive('removeResponseHeader')
            ->with('Location');

        $response = $this->webToPayProvider->getPaymentRedirect($redirectRequest);

        $this->assertInstanceOf(PaymentRedirectResponse::class, $response);
        $this->assertEquals('http://example.paysera.test', $response->getRedirectUrl());
    }

    public function testGetPaymentRedirectWithScript(): void
    {
        [$redirectRequest, $providerData] = $this->getPaymentRedirectRequestAndProviderData();

        m::mock('overload:'. WebToPay::class)
            ->expects()
            ->redirectToPayment($providerData, false);

        $this->redirectResponseHelper->shouldReceive('catchOutputBuffer')
            ->andReturn('<script type="text/javascript">window.location = "' . addslashes('http://example.paysera.test?test\'test') . '";</script>')
            ->shouldReceive('getResponseHeaders')
            ->andReturn([])
            ->shouldReceive('removeResponseHeader')
            ->with('Location');

        $response = $this->webToPayProvider->getPaymentRedirect($redirectRequest);

        $this->assertInstanceOf(PaymentRedirectResponse::class, $response);
        $this->assertEquals('http://example.paysera.test?test\'test', $response->getRedirectUrl());
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

        $validationResponseMock = m::mock(PaymentCallbackValidationResponse::class);
        $this->paymentValidationResponseNormalizer->expects()
            ->denormalize(['some data here...'])
            ->andReturn($validationResponseMock);

        $validateResponse = $this->webToPayProvider->getPaymentCallbackValidatedData($validationRequest);

        $this->assertEquals(
            $validationResponseMock,
            $validateResponse,
            'The provider must return validation response.'
        );
    }

    public function testGetPaymentRedirectProviderExceptions(): void
    {
        m::mock('overload:'. WebToPay::class)
            ->shouldReceive('redirectToPayment')
            ->once()
            ->withAnyArgs()
            ->andThrow(new WebToPayException('Some troubles.'));

        $this->redirectResponseHelper->shouldReceive('catchOutputBuffer')
            ->once()
            ->andThrow(new WebToPayException('Some troubles.'));

        $this->expectException(ProviderException::class);
        $this->expectExceptionMessage('Provider thrown exception.');
        $this->expectExceptionCode(BaseException::E_PROVIDER_ISSUE);

        $request = new PaymentRedirectRequest(
            1,
            'pass',
            'acceptUrl',
            'cancelUrl',
            'callbackUrl',
            new Order(1, 100, 'USD')
        );

        $this->webToPayProvider->getPaymentRedirect($request);
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
            ->andThrow(new WebToPayException('Some troubles.'));

        $this->expectException(ProviderException::class);
        $this->expectExceptionMessage('Provider thrown exception.');
        $this->expectExceptionCode(BaseException::E_PROVIDER_ISSUE);

        $this->webToPayProvider->{$method}($request);
    }

    public function exceptionsProvider(): array
    {
        return [
            [
                'WebToPayProvider method' => 'getPaymentMethodCountries',
                'Request argument' => new PaymentMethodsRequest(
                    1,
                    100,
                    'USD'
                ),
                'WebToPay static method' => 'getPaymentMethodList',
            ],
            [
                'WebToPayProvider method' => 'getPaymentCallbackValidatedData',
                'Request argument' => new PaymentCallbackValidationRequest(
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
            100,
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
            ->setTest(true);

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
     * @return array{0: PaymentCallbackValidationRequest, 1: array}
     */
    protected function getPaymentValidationRequestAndProviderData(): array
    {
        $validationRequest = (new PaymentCallbackValidationRequest(
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
