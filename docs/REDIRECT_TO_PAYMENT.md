## Redirect to payment
Redirecting to the chosen payment method page.

## Basic usage
```php
<?php

use Paysera\CheckoutSdk\CheckoutFacade;
use Paysera\CheckoutSdk\CheckoutFacadeFactory;
use Paysera\CheckoutSdk\Entity\Order;
use Paysera\CheckoutSdk\Entity\PaymentRedirectRequest;

...

$checkoutFacade = (new CheckoutFacadeFactory)->create();

$order = new Order(
    (int) $data['order_id'],
    (float) $data['order_amount_in_cents'],
    (string) $order['order_currency_code']
);
$order->setPaymentFirstName($data['order_payment_firstname'])
    ->setPaymentLastName($data['order_payment_lastname'])
    ->setPaymentEmail($data['order_payment_email'])
    ->setPaymentStreet($data['order_payment_address'])
    ->setPaymentCity($data['order_payment_city'])
    ->setPaymentZip($data['order_payment_postcode'])
    ->setPaymentCountryCode($data['order_payment_country'])
;
$redirectRequest = new PaymentRedirectRequest(
    (int) $data['project_id'],
    (string) $data['project_password'],
    (string) $data['accept_url'],
    (string) $data['cancel_url'],
    (string) $data['callback_url'],
    $order
);
$redirectRequest->setPayment($data['paysera_payment_method']);

$checkoutFacade->redirectToPayment($redirectRequest);
```

Method `redirectToPayment()` makes redirect to the chosen payment method page.
