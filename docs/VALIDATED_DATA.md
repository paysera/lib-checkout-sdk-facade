## Payment validated data
Receiving [callback](https://developers.paysera.com/en/checkout/integrations/integration-callback) validated data for order status checking.

## Basic usage

```php
<?php

use Paysera\CheckoutSdk\CheckoutFacade;
use Paysera\CheckoutSdk\CheckoutFacadeFactory;
use Paysera\CheckoutSdk\Entity\Request\PaymentCallbackValidationRequest;

...

$checkoutFacade = (new CheckoutFacadeFactory)->create();

$paymentValidationRequest = new PaymentCallbackValidationRequest(
    (int) $data['project_id'],
    (string) $data['project_password'],
    (string) $data['payment_request_data']
);
$paymentValidationRequest->setSs1($data['payment_request_ss1'])
    ->setSs2($data['payment_request_ss2']);

$response = $checkoutFacade->getPaymentCallbackValidatedData($paymentValidationRequest);
```

Method `getPaymentCallbackValidatedData()` returns [response](../src/Entity/PaymentCallbackValidationResponse.php) with all decoded information about payment.
