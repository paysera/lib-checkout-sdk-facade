<?php

declare(strict_types=1);

namespace Paysera\CheckoutSdk\Entity;

use Paysera\CheckoutSdk\Entity\Collection\PaymentMethodGroupCollection;
use Paysera\CheckoutSdk\Entity\Collection\TranslationCollection;

class PaymentMethodCountry extends AbstractPaymentMethod
{
    /**
     * Current country code (country code ISO2)
     */
    protected string $countryCode;

    /**
     * Holds available payment types for this country.
     */
    protected PaymentMethodGroupCollection $groups;

    public function __construct(string $countryCode, string $defaultLanguage = self::DEFAULT_LANGUAGE)
    {
        $this->countryCode = strtolower($countryCode);

        $this->groups = new PaymentMethodGroupCollection();
        $this->titleTranslations = new TranslationCollection();

        $this->setDefaultLanguage($defaultLanguage);
    }

    /**
     * @inheritDoc
     */
    public function setDefaultLanguage(string $language): self
    {
        parent::setDefaultLanguage($language);

        foreach ($this->groups as $group) {
            $group->setDefaultLanguage($language);
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
        return $this->translate($this->titleTranslations, $language, $this->defaultLanguage, $this->countryCode);
    }

    /**
     * Gets country code (country code ISO2).
     */
    public function getCode(): string
    {
        return $this->countryCode;
    }

    /**
     * Returns collection of payment method groups registered for this country.
     */
    public function getGroups(): PaymentMethodGroupCollection
    {
        return $this->groups;
    }

    /**
     * Returns whether this country has no groups.
     */
    public function isEmpty(): bool
    {
        return $this->groups->count() === 0;
    }
}
