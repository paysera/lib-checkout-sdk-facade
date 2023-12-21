## Payment methods
You can get available payment [types](https://developers.paysera.com/en/checkout/payment-types) for your project in real time, before redirecting the user to Paysera system. It might be useful if you want to display the choice of payment methods on your website - in this case, you do not have to update configuration each time something changes.

## Basic usage

```php
<?php

use Paysera\CheckoutSdk\CheckoutFacade;
use Paysera\CheckoutSdk\CheckoutFacadeFactory;
use Paysera\CheckoutSdk\Entity\Request\PaymentMethodsRequest;

...

$checkoutFacade = (new CheckoutFacadeFactory)->create();

$request = new PaymentMethodsRequest(
    (int) $data['project_id'],
    (int) $data['order_amount_in_cents'],
    (string) $data['order_currency_code']
);

$collection = $checkoutFacade->getPaymentMethods($this->request);
```

Method `getPaymentMethods()` returns a [collection](../src/Entity/Collection/PaymentMethodCountryCollection.php) of [objects](../src/Entity/PaymentMethodCountry.php) with all available payment methods grouped by countries.

> [!IMPORTANT]  
> Returned collection will be filtered by `amount` value by default.

## Filtering collection

```php
<?php

use Paysera\CheckoutSdk\CheckoutFacade;
use Paysera\CheckoutSdk\Entity\Request\PaymentMethodsRequest;
use Paysera\CheckoutSdk\Entity\Collection\PaymentMethodCountryCollection;
use Paysera\CheckoutSdk\Entity\PaymentMethodCountry;

...

/** @var PaymentMethodCountryCollection $collection */
$collection = $checkoutFacade->getPaymentMethods($request);

/** @var PaymentMethodCountryCollection $filteredCollection */
$filteredCollection = $collection->filter(
    fn (PaymentMethodCountry $country) => $country->getCode() === 'lt'
);
```
You will receive new filtered collection after using `filter()` method.

## Iterating collection

```php
<?php

use Paysera\CheckoutSdk\CheckoutFacade;
use Paysera\CheckoutSdk\Entity\Request\PaymentMethodsRequest;
use Paysera\CheckoutSdk\Entity\Collection\PaymentMethodCountryCollection;
use Paysera\CheckoutSdk\Entity\PaymentMethodCountry;

...

/** @var PaymentMethodCountryCollection $collection */
$collection = $checkoutFacade->getPaymentMethods($request);

/** @var PaymentMethodCountry $paymentMethodCountry */
foreach ($collection as $paymentMethodCountry) {
    echo $paymentMethodCountry->getCode();
}
```
Docblock annotations are used only for showing the type of returned entities and can be omitted in real code.

## Get translations for collection items

```php
<?php

use Paysera\CheckoutSdk\CheckoutFacade;
use Paysera\CheckoutSdk\Entity\Request\PaymentMethodsRequest;
use Paysera\CheckoutSdk\Entity\Collection\PaymentMethodCollection;
use Paysera\CheckoutSdk\Entity\PaymentMethodCountry;
use Paysera\CheckoutSdk\Service\Translator;

...

$translator = new Translator();
$data = [];

/** @var PaymentMethodCollection $collection */
$collection = $checkoutFacade->getPaymentMethods($request);

/** @var PaymentMethod $paymentMethod */
foreach ($collection as $paymentMethod) {
    $data[] = [
        'title' => $translator->getTitle($paymentMethod, 'lt'),
        'logo' => $translator->getLogo($paymentMethod, 'lt')
    ];
}
```
You can use translator `getTitle()` method for [PaymentMethodCountry](../src/Entity/PaymentMethodCountry.php), [PaymentMethodGroup](../src/Entity/PaymentMethodGroup.php) or [PaymentMethod](../src/Entity/PaymentMethod.php) object. Translator `getLogo()` method is available only for [PaymentMethod](../src/Entity/PaymentMethod.php) object.

### Another approach:

```php
<?php

use Paysera\CheckoutSdk\CheckoutFacade;
use Paysera\CheckoutSdk\Entity\Request\PaymentMethodsRequest;
use Paysera\CheckoutSdk\Entity\Collection\PaymentMethodCollection;
use Paysera\CheckoutSdk\Entity\PaymentMethodCountry;
use Paysera\CheckoutSdk\Service\Translator;

...

$translator = new Translator();
$data = [];

/** @var PaymentMethodCollection $collection */
$collection = $checkoutFacade->getPaymentMethods($request);

/** @var PaymentMethod $paymentMethod */
foreach ($collection as $paymentMethod) {
    $data[] = [
        'title' => $translator->translate($paymentMethod->getTitleTranslations(), 'lt') ?? $paymentMethod->getKey(),
        'logo' => $translator->translate($paymentMethod->getLogos(), 'lt')
    ];
}
```

Docblock annotations are used only for showing the type of returned entities and can be omitted in real code.

