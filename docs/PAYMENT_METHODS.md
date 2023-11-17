## Payment methods
Receiving and working with paysera payment methods.

## Basic usage

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

$collection = $checkoutFacade->getPaymentMethodCountries($this->request);
```

Method `getPaymentMethodCountries()` returns a [collection](../src/Entity/Collection/PaymentMethodCountryCollection.php) of [objects](../src/Entity/PaymentMethodCountry.php) with all available payment methods grouped by countries.

> [!IMPORTANT]  
> Returned collection will be filtered by `amount` value by default.

## Filtering by predefined countries list

```php
<?php

use Paysera\CheckoutSdk\CheckoutFacade;
use Paysera\CheckoutSdk\Entity\Request\PaymentMethodsRequest;

...

$request = new PaymentMethodsRequest(
    (int) $data['project_id'],
    (string) $data['language'],
    $order,
    ['lt', 'lv']
);

$collection = $checkoutFacade->getPaymentMethodCountries($request);
```
## Iterating collection

```php
<?php

use Paysera\CheckoutSdk\CheckoutFacade;
use Paysera\CheckoutSdk\Entity\Request\PaymentMethodsRequest;
use Paysera\CheckoutSdk\Entity\Collection\PaymentMethodCountryCollection;
use Paysera\CheckoutSdk\Entity\PaymentMethodCountry;

...

/** @var PaymentMethodCountryCollection $collection */
$collection = $checkoutFacade->getPaymentMethodCountries($request);

/** @var PaymentMethodCountry $paymentMethodCountry */
foreach ($collection as $paymentMethodCountry) {
    echo $paymentMethodCountry->getCode();
}
```
Docblock annotations are used only for showing the type of returned entities and can be omitted in real code.

