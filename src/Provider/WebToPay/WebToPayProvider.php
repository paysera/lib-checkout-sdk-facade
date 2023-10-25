<?php

declare(strict_types=1);

namespace Paysera\CheckoutSdk\Provider\WebToPay;

use Paysera\CheckoutSdk\Entity\Collection\PaymentMethodCountryCollection;
use Paysera\CheckoutSdk\Entity\PaymentMethodRequest;
use Paysera\CheckoutSdk\Entity\PaymentRedirectRequest;
use Paysera\CheckoutSdk\Entity\PaymentValidationRequest;
use Paysera\CheckoutSdk\Entity\PaymentValidationResponse;
use Paysera\CheckoutSdk\Exception\ProviderException;
use Paysera\CheckoutSdk\Provider\ProviderInterface;
use Paysera\CheckoutSdk\Provider\WebToPay\Adapter\PaymentMethodCountryAdapter;
use Paysera\CheckoutSdk\Provider\WebToPay\Adapter\PaymentValidationResponseAdapter;
use WebToPay;
use WebToPayException;

class WebToPayProvider implements ProviderInterface
{
    protected PaymentMethodCountryAdapter $paymentMethodCountryAdapter;
    protected PaymentValidationResponseAdapter $paymentValidationResponseAdapter;

    public function __construct(
        PaymentMethodCountryAdapter $paymentMethodCountryAdapter,
        PaymentValidationResponseAdapter $paymentValidationResponseAdapter
    ) {
        $this->paymentMethodCountryAdapter = $paymentMethodCountryAdapter;
        $this->paymentValidationResponseAdapter = $paymentValidationResponseAdapter;
    }

    public function getPaymentMethodCountries(PaymentMethodRequest $request): PaymentMethodCountryCollection
    {
        $countryCollection = new PaymentMethodCountryCollection();

        try {
            $countryList = WebToPay::getPaymentMethodList(
                $request->getProjectId(),
                $request->getOrder()->getAmount(),
                $request->getOrder()->getCurrency()
            );

            $countries = $countryList->setDefaultLanguage($request->getLanguage())
                ->getCountries()
            ;
        } catch (WebToPayException $exception) {
            throw new ProviderException($exception);
        }

        foreach ($countries as $country) {
            $adaptedPaymentMethodCountry = $this->paymentMethodCountryAdapter->convert($country);
            $countryCollection->append($adaptedPaymentMethodCountry);
        }

        return $countryCollection;
    }

    public function redirectToPayment(PaymentRedirectRequest $request): void
    {
        $paymentData = $this->getRedirectPaymentDataFromRequest($request);

        try {
            WebToPay::redirectToPayment($paymentData, true);
        } catch (WebToPayException $exception) {
            throw new ProviderException($exception);
        }
    }

    public function validatePayment(PaymentValidationRequest $request): PaymentValidationResponse
    {
        $validatePaymentData = $this->getValidatePaymentDataFromRequest($request);

        try {
            $response = WebToPay::validateAndParseData(
                $validatePaymentData,
                $request->getProjectId(),
                $request->getProjectPassword()
            );

            return $this->paymentValidationResponseAdapter->convert($response);
        } catch (WebToPayException $exception) {
            throw new ProviderException($exception);
        }
    }

    protected function getValidatePaymentDataFromRequest(PaymentValidationRequest $request): array
    {
        $validatePaymentData = [
            'data' => $request->getData(),
            'ss1' => $request->getSs1(),
            'ss2' => $request->getSs2(),
            'type' => $request->getType(),
            'to' => $request->getTo(),
            'from' => $request->getFrom(),
            'sms' => $request->getSms(),
        ];

        return array_filter($validatePaymentData, static fn ($value) => $value !== null);
    }

    protected function getRedirectPaymentDataFromRequest(PaymentRedirectRequest $request): array
    {
        $paymentData = [
            'projectid' => $request->getProjectId(),
            'sign_password' => $request->getProjectPassword(),
            'orderid' => $request->getOrder()->getOrderId(),
            'amount' => (int) $request->getOrder()->getAmount(),
            'currency' => $request->getOrder()->getCurrency(),
            'accepturl' => $request->getAcceptUrl(),
            'cancelurl' => $request->getCancelUrl(),
            'callbackurl' => $request->getCallbackUrl(),
            'payment' => $request->getPayment(),
            'country' => $request->getOrder()->getPaymentCountryCode(),
            'p_firstname' => $request->getOrder()->getPaymentFirstName(),
            'p_lastname' => $request->getOrder()->getPaymentLastName(),
            'p_email' => $request->getOrder()->getPaymentEmail(),
            'p_street' => $request->getOrder()->getPaymentStreet(),
            'p_city' => $request->getOrder()->getPaymentCity(),
            'p_zip' => $request->getOrder()->getPaymentZip(),
            'p_state' => $request->getOrder()->getPaymentState(),
            'p_countrycode' => $request->getOrder()->getPaymentCountryCode(),
            'test' => (int) $request->getTest(),
        ];

        return array_filter($paymentData, static fn ($value) => $value !== null);
    }
}
