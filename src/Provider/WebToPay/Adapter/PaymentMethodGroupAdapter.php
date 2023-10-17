<?php

declare(strict_types=1);

namespace Paysera\CheckoutSdk\Provider\WebToPay\Adapter;

use Paysera\CheckoutSdk\Entity\PaymentMethodGroup;
use Paysera\CheckoutSdk\Entity\Translation;
use Paysera\CheckoutSdk\Util\Invader;
use WebToPay_PaymentMethodGroup;

class PaymentMethodGroupAdapter
{
    protected PaymentMethodAdapter $paymentMethodAdapter;

    public function __construct(PaymentMethodAdapter $paymentMethodAdapter)
    {
        $this->paymentMethodAdapter = $paymentMethodAdapter;
    }

    public function convert(WebToPay_PaymentMethodGroup $providerEntity): PaymentMethodGroup
    {
        $providerEntityProperties = Invader::getProperties($providerEntity);

        $paymentMethodGroup = new PaymentMethodGroup(
            (string) $providerEntityProperties['groupKey'],
            (string) $providerEntityProperties['defaultLanguage']
        );

        foreach ($providerEntityProperties['translations'] ?? [] as $language => $titleTranslation) {
            $translation = new Translation($language, $titleTranslation);
            $paymentMethodGroup->getTitleTranslations()->append($translation);
        }

        foreach ($providerEntityProperties['paymentMethods'] ?? [] as $paymentMethod) {
            $adaptedPaymentMethod = $this->paymentMethodAdapter->convert($paymentMethod);
            $paymentMethodGroup->getPaymentMethods()->append($adaptedPaymentMethod);
        }

        return $paymentMethodGroup;
    }
}
