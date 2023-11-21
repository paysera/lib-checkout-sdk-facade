<?php

declare(strict_types=1);

namespace Paysera\CheckoutSdk;

use Paysera\CheckoutSdk\Entity\Collection\PaymentMethodCountryCollection;
use Paysera\CheckoutSdk\Entity\Order;
use Paysera\CheckoutSdk\Entity\PaymentMethodCountry;
use Paysera\CheckoutSdk\Entity\Request\PaymentMethodsRequest;
use Paysera\CheckoutSdk\Entity\Request\PaymentRedirectRequest;
use Paysera\CheckoutSdk\Entity\Request\PaymentCallbackValidationRequest;
use Paysera\CheckoutSdk\Entity\PaymentCallbackValidationResponse;
use Paysera\CheckoutSdk\Entity\PaymentRedirectResponse;
use Paysera\CheckoutSdk\Exception\CallbackValidationException;
use Paysera\CheckoutSdk\Provider\ProviderInterface;
use Paysera\CheckoutSdk\Service\PaymentMethodCountryManager;
use Paysera\CheckoutSdk\Service\PaymentStatus;
use Paysera\CheckoutSdk\Validator\PaymentCallbackValidator;
use Paysera\CheckoutSdk\Validator\RequestValidator;

final class CheckoutFacade
{
    private ProviderInterface $provider;
    private RequestValidator $requestValidator;
    private PaymentCallbackValidator $paymentCallbackValidator;

    public function __construct(
        ProviderInterface $provider,
        RequestValidator $requestValidator,
        PaymentCallbackValidator $paymentCallbackValidator
    ) {
        $this->provider = $provider;
        $this->requestValidator = $requestValidator;
        $this->paymentCallbackValidator = $paymentCallbackValidator;
    }

    /**
     * @param PaymentMethodsRequest $request
     * @return PaymentMethodCountryCollection<PaymentMethodCountry>
     */
    public function getPaymentMethods(PaymentMethodsRequest $request): PaymentMethodCountryCollection
    {
        $this->requestValidator->validate($request);

        return $this->provider->getPaymentMethods($request);
    }

    public function getPaymentRedirect(PaymentRedirectRequest $request): PaymentRedirectResponse
    {
        $this->requestValidator->validate($request);

        return $this->provider->getPaymentRedirect($request);
    }

    /**
     * Returns the validated callback data.
     * If the second argument is specified, checks that the amount and currency of the order and response match.
     * Also checks payment status.
     */
    public function getPaymentCallbackValidatedData(
        PaymentCallbackValidationRequest $request,
        Order $order = null
    ): PaymentCallbackValidationResponse {
        $this->requestValidator->validate($request);

        $response = $this->provider->getPaymentCallbackValidatedData($request);

        return $order === null ? $response : $this->paymentCallbackValidator->validate($response, $order);
    }
}
