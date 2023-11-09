<?php

declare(strict_types=1);

namespace Paysera\CheckoutSdk\Provider\WebToPay;

use Paysera\CheckoutSdk\Entity\Collection\PaymentMethodCountryCollection;
use Paysera\CheckoutSdk\Entity\PaymentMethodCountry;
use Paysera\CheckoutSdk\Entity\PaymentMethodRequest;
use Paysera\CheckoutSdk\Entity\PaymentRedirectRequest;
use Paysera\CheckoutSdk\Entity\PaymentCallbackValidationRequest;
use Paysera\CheckoutSdk\Entity\PaymentCallbackValidationResponse;
use Paysera\CheckoutSdk\Exception\ProviderException;
use Paysera\CheckoutSdk\Provider\ProviderInterface;
use Paysera\CheckoutSdk\Provider\WebToPay\Adapter\PaymentCallbackValidationRequestNormalizer;
use Paysera\CheckoutSdk\Provider\WebToPay\Adapter\PaymentMethodCountryAdapter;
use Paysera\CheckoutSdk\Provider\WebToPay\Adapter\PaymentRedirectRequestNormalizer;
use Paysera\CheckoutSdk\Provider\WebToPay\Adapter\PaymentValidationResponseNormalizer;
use WebToPay;
use WebToPayException;

class WebToPayProvider implements ProviderInterface
{
    protected PaymentMethodCountryAdapter $paymentMethodCountryAdapter;
    protected PaymentValidationResponseNormalizer $paymentValidationResponseNormalizer;
    protected PaymentRedirectRequestNormalizer $paymentRedirectRequestNormalizer;
    protected PaymentCallbackValidationRequestNormalizer $callbackValidationRequestNormalizer;

    public function __construct(
        PaymentMethodCountryAdapter $paymentMethodCountryAdapter,
        PaymentValidationResponseNormalizer $paymentValidationResponseNormalizer,
        PaymentRedirectRequestNormalizer $paymentRedirectRequestNormalizer,
        PaymentCallbackValidationRequestNormalizer $callbackValidationRequestNormalizer
    ) {
        $this->paymentMethodCountryAdapter = $paymentMethodCountryAdapter;
        $this->paymentValidationResponseNormalizer = $paymentValidationResponseNormalizer;
        $this->paymentRedirectRequestNormalizer = $paymentRedirectRequestNormalizer;
        $this->callbackValidationRequestNormalizer = $callbackValidationRequestNormalizer;
    }

    /**
     * @param PaymentMethodRequest $request
     * @return PaymentMethodCountryCollection<PaymentMethodCountry>
     * @throws ProviderException
     */
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
        $paymentData = $this->paymentRedirectRequestNormalizer->normalize($request);

        try {
            WebToPay::redirectToPayment($paymentData, true);
        } catch (WebToPayException $exception) {
            throw new ProviderException($exception);
        }
    }

    public function getPaymentCallbackValidationData(
        PaymentCallbackValidationRequest $request
    ): PaymentCallbackValidationResponse {
        $validatePaymentData = $this->callbackValidationRequestNormalizer->normalize($request);

        try {
            $response = WebToPay::validateAndParseData(
                $validatePaymentData,
                $request->getProjectId(),
                $request->getProjectPassword()
            );

            return $this->paymentValidationResponseNormalizer->denormalize($response);
        } catch (WebToPayException $exception) {
            throw new ProviderException($exception);
        }
    }
}
