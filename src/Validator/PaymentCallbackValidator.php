<?php

declare(strict_types=1);

namespace Paysera\CheckoutSdk\Validator;

use Paysera\CheckoutSdk\Entity\Order;
use Paysera\CheckoutSdk\Entity\PaymentCallbackValidationResponse;
use Paysera\CheckoutSdk\Exception\CallbackValidationException;
use Paysera\CheckoutSdk\Service\PaymentStatus;

class PaymentCallbackValidator
{
    public function validate(PaymentCallbackValidationResponse $response, Order $order): void
    {
        if ($response->getStatus() !== PaymentStatus::SUCCESS) {
            throw new CallbackValidationException("Payment status `{$response->getStatus()}` is not successful.");
        }

        if (
            $response->getOrder()->getAmount() !== $order->getAmount()
            || $response->getOrder()->getCurrency() !== $order->getCurrency()
        ) {
            if ($response->getPaymentAmount() === null) {
                throw new CallbackValidationException(
                    'Wrong pay amount: '
                    . $response->getOrder()->getAmount() / 100
                    . $response->getOrder()->getCurrency()
                    . ', expected: '
                    . $order->getAmount() / 100
                    . $order->getCurrency()
                );
            }
            if (
                $response->getPaymentAmount() !== $order->getAmount()
                || $response->getPaymentCurrency() !== $order->getCurrency()
            ) {
                throw new CallbackValidationException(
                    'Wrong pay amount: '
                    . $response->getPaymentAmount() / 100
                    . $response->getPaymentCurrency()
                    . ', expected: '
                    . $order->getAmount() / 100
                    . $order->getCurrency()
                );
            }
        }
    }
}
