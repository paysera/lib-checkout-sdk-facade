## Payment redirect
Receiving [redirect](https://developers.paysera.com/en/checkout/integrations/integration-specification) url to the chosen payment method page.

## Basic usage

```php
<?php

use Paysera\CheckoutSdk\CheckoutFacade;
use Paysera\CheckoutSdk\CheckoutFacadeFactory;
use Paysera\CheckoutSdk\Entity\Order;
use Paysera\CheckoutSdk\Entity\Request\PaymentRedirectRequest;

...

$checkoutFacade = (new CheckoutFacadeFactory)->create();

$order = new Order(
    (int) $data['order_id'],
    (int) $data['order_amount_in_cents'],
    (string) $order['order_currency_code']
);
$order->setPayerFirstName($data['firstname'])
    ->setPayerLastName($data['lastname'])
    ->setPayerEmail($data['email']);

$redirectRequest = new PaymentRedirectRequest(
    (int) $data['project_id'],
    (string) $data['project_password'],
    (string) $data['accept_url'],
    (string) $data['cancel_url'],
    (string) $data['callback_url'],
    $order
);

$paymentRedirectResponse = $checkoutFacade->getPaymentRedirect($redirectRequest);

...

$response->redirect($paymentRedirectResponse->getRedirectUrl());
```

Method `getPaymentRedirect()` returns a [response](../src/Entity/PaymentRedirectResponse.php) which contains `url` of the Paysera payment page. Use this `url`in your code and make a redirect in the chosen way.
