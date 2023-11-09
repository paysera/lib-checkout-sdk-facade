<?php

declare(strict_types=1);

namespace Paysera\CheckoutSdk\Entity;

use Paysera\CheckoutSdk\Entity\Collection\PaymentMethodCollection;
use Paysera\CheckoutSdk\Entity\Collection\TranslationCollection;

class PaymentMethodGroup extends AbstractPaymentMethod
{
    /**
     * Some unique (in the scope of country) key for this group.
     */
    protected string $key;

    /**
     * Holds actual payment methods.
     * @var PaymentMethodCollection<PaymentMethod>
     */
    protected PaymentMethodCollection $paymentMethods;

    public function __construct(string $key, string $defaultLanguage = self::DEFAULT_LANGUAGE)
    {
        parent::__construct();

        $this->key = $key;

        $this->paymentMethods = new PaymentMethodCollection();
        $this->titleTranslations = new TranslationCollection();

        $this->setDefaultLanguage($defaultLanguage);
    }

    /**
     * @inheritDoc
     */
    public function setDefaultLanguage(string $language): self
    {
        parent::setDefaultLanguage($language);

        foreach ($this->paymentMethods as $paymentMethod) {
            $paymentMethod->setDefaultLanguage($language);
        }

        return $this;
    }

    /**
     * Gets title of the group. Tries to get title in specified language. If it is not found or if language is not
     * specified, uses default language, given to constructor.
     *
     * @param string|null $language [Optional]
     */
    public function getTitle(string $language = null): string
    {
        return $this->translator->translate(
            $this->titleTranslations,
            $this->getDefaultLanguage(),
            $language
        ) ?? $this->key;
    }

    /**
     * Returns group key.
     */
    public function getKey(): string
    {
        return $this->key;
    }

    /**
     * Returns available payment methods for this group.
     * @return PaymentMethodCollection<PaymentMethod>
     */
    public function getPaymentMethods(): PaymentMethodCollection
    {
        return $this->paymentMethods;
    }

    /**
     * Returns whether this group has no payment methods.
     */
    public function isEmpty(): bool
    {
        return $this->paymentMethods->count() === 0;
    }
}
