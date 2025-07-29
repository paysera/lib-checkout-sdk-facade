## Check merchant order is paid
You can make simple check of merchant order with `isMerchantOrderPaid()` method. It checks payment status is `success`. Also, it checks `amount` and `currency` of merchant order match Paysera payment `amount` and `currecy`.

## Basic usage

```php
<?php

use Paysera\CheckoutSdk\CheckoutFacade;
use Paysera\CheckoutSdk\CheckoutFacadeFactory;
use Paysera\CheckoutSdk\Entity\Request\PaymentCallbackValidationRequest;
use Paysera\CheckoutSdk\Entity\Order;

...

$merchantOrder = new Order(
    (int) $data['order_id'],
    (int) $data['order_amount_in_cents'],
    (string) $order['order_currency_code']
);
$checkoutFacade = (new CheckoutFacadeFactory)->create();

$paymentValidationRequest = new PaymentCallbackValidationRequest(
    (int) $data['project_id'],
    (string) $data['project_password'],
    (string) $data['payment_request_data']
);
$paymentValidationRequest->setSs1($data['payment_request_ss1'])
    ->setSs2($data['payment_request_ss2'])
    ->setSs3($data['payment_request_ss3']);

$response = $checkoutFacade->getPaymentCallbackValidatedData($paymentValidationRequest);
$isPaid = $checkoutFacade->isMerchantOrderPaid($response, $merchantOrder);

if ($isPaid) {
    // Do something (update order or etc.)
}
```
