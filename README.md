## Checkout SDK
PHP SDK facade for checkout processing

## Code style
[Paysera PHP style](https://github.com/paysera/php-style-guide)

## Used
<b>Built with</b>
- [WebToPay](https://github.com/paysera/lib-webtopay)

## Installation (will be updated soon)
While in under developing:
```
{
    "require": {
        "php": ">=7.4",
        "paysera/lib-checkout-sdk-facade": "dev-EC-867",
    },
    "repositories": [
        {
            "type": "vcs",
            "url": "https://github.com/enigma-tm/lib-checkout-sdk-facade"
        }
    ]
}
```

## Simple usage example

```php
<?php

use Paysera\CheckoutSdk\CheckoutFacade;
use Paysera\CheckoutSdk\CheckoutFacadeFactory;
use Paysera\CheckoutSdk\Entity\Order;
use Paysera\CheckoutSdk\Entity\Request\PaymentMethodsRequest;

...

$checkoutFacade = (new CheckoutFacadeFactory)->create();

$order = new Order(
    (int) $data['order_id'],
    (float) $data['order_amount_in_cents'],
    (string) $data['order_currency_code']
 );
$request = new PaymentMethodsRequest(
    (int) $data['project_id'],
    (string) $data['language'],
    $order
);

$collection = $checkoutFacade->getPaymentMethodCountries($request);
```

## Use cases
- [Payment methods](docs/PAYMENT_METHODS.md)
- [Redirect to payment](docs/REDIRECT_TO_PAYMENT.md)
- [Validate Payment](docs/VALIDATE_PAYMENT.md)
