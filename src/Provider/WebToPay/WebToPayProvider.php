<?php

declare(strict_types=1);

namespace Paysera\CheckoutSdk\Provider\WebToPay;

use Paysera\CheckoutSdk\Entity\Collection\PaymentMethodCountryCollection;
use Paysera\CheckoutSdk\Entity\PaymentMethodCountry;
use Paysera\CheckoutSdk\Entity\Request\PaymentMethodsRequest;
use Paysera\CheckoutSdk\Entity\Request\PaymentRedirectRequest;
use Paysera\CheckoutSdk\Entity\Request\PaymentCallbackValidationRequest;
use Paysera\CheckoutSdk\Entity\PaymentCallbackValidationResponse;
use Paysera\CheckoutSdk\Entity\PaymentRedirectResponse;
use Paysera\CheckoutSdk\Exception\ProviderException;
use Paysera\CheckoutSdk\Exception\ValidationException;
use Paysera\CheckoutSdk\Provider\ProviderInterface;
use Paysera\CheckoutSdk\Provider\WebToPay\Adapter\PaymentCallbackValidationRequestNormalizer;
use Paysera\CheckoutSdk\Provider\WebToPay\Adapter\PaymentMethodCountryAdapter;
use Paysera\CheckoutSdk\Provider\WebToPay\Adapter\PaymentRedirectRequestNormalizer;
use Paysera\CheckoutSdk\Provider\WebToPay\Adapter\PaymentValidationResponseNormalizer;
use Paysera\CheckoutSdk\Provider\WebToPay\Helper\RedirectToPaymentHelper;
use WebToPay;
use WebToPayException;

class WebToPayProvider implements ProviderInterface
{
    protected PaymentMethodCountryAdapter $paymentMethodCountryAdapter;
    protected PaymentValidationResponseNormalizer $paymentValidationResponseNormalizer;
    protected PaymentRedirectRequestNormalizer $paymentRedirectRequestNormalizer;
    protected PaymentCallbackValidationRequestNormalizer $callbackValidationRequestNormalizer;
    protected RedirectToPaymentHelper $redirectToPaymentHelper;

    public function __construct(
        PaymentMethodCountryAdapter $paymentMethodCountryAdapter,
        PaymentValidationResponseNormalizer $paymentValidationResponseNormalizer,
        PaymentRedirectRequestNormalizer $paymentRedirectRequestNormalizer,
        PaymentCallbackValidationRequestNormalizer $callbackValidationRequestNormalizer,
        RedirectToPaymentHelper $redirectToPaymentHelper
    ) {
        $this->paymentMethodCountryAdapter = $paymentMethodCountryAdapter;
        $this->paymentValidationResponseNormalizer = $paymentValidationResponseNormalizer;
        $this->paymentRedirectRequestNormalizer = $paymentRedirectRequestNormalizer;
        $this->callbackValidationRequestNormalizer = $callbackValidationRequestNormalizer;
        $this->redirectToPaymentHelper = $redirectToPaymentHelper;
    }

    /**
     * @param PaymentMethodsRequest $request
     * @return PaymentMethodCountryCollection<PaymentMethodCountry>
     * @throws ProviderException
     */
    public function getPaymentMethodCountries(PaymentMethodsRequest $request): PaymentMethodCountryCollection
    {
        $countryCollection = new PaymentMethodCountryCollection();

        try {
            $countries = WebToPay::getPaymentMethodList(
                $request->getProjectId(),
                $request->getAmount(),
                $request->getCurrency()
            )->getCountries();
        } catch (WebToPayException $exception) {
            throw new ProviderException($exception);
        }

        foreach ($countries as $country) {
            $adaptedPaymentMethodCountry = $this->paymentMethodCountryAdapter->convert($country);
            $countryCollection->append($adaptedPaymentMethodCountry);
        }

        return $countryCollection;
    }

    /**
     * @throws ValidationException
     * @throws ProviderException
     * @param PaymentRedirectRequest $request
     */
    public function getPaymentRedirect(PaymentRedirectRequest $request): PaymentRedirectResponse
    {
        $paymentData = $this->paymentRedirectRequestNormalizer->normalize($request);

        try {
            $providerOutput = $this->redirectToPaymentHelper
                ->catchOutputBuffer(fn () => WebToPay::redirectToPayment($paymentData, false))
            ;
        } catch (WebToPayException $exception) {
            throw new ProviderException($exception);
        }

        $redirectUrl =
            $this->getRedirectUrlFromHeaders($this->redirectToPaymentHelper->getResponseHeaders())
            ?? $this->getRedirectUrlFromScript($providerOutput)
        ;
        $this->redirectToPaymentHelper->removeResponseHeader('Location');

        if ($redirectUrl === null) {
            throw new ValidationException('Redirect url must be not empty.');
        }

        return new PaymentRedirectResponse($redirectUrl);
    }

    public function getPaymentCallbackValidatedData(
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

    /**
     * @param array<int, string> $headers
     * @return string|null
     */
    protected function getRedirectUrlFromHeaders(array $headers): ?string
    {
        foreach ($headers as $header) {
            if (strpos($header, 'Location:') !== false) {
                return trim(substr($header, strlen('Location:')));
            }
        }

        return null;
    }

    protected function getRedirectUrlFromScript(string $providerOutput): ?string
    {
        if (preg_match('/window.location\s*=\s*"(.+?)"/', $providerOutput, $matches)) {
            return stripslashes($matches[1]);
        }

        return null;
    }
}
