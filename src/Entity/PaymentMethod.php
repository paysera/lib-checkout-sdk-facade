<?php

declare(strict_types=1);

namespace Paysera\CheckoutSdk\Entity;

use Paysera\CheckoutSdk\Entity\Collection\TranslationCollection;

class PaymentMethod extends AbstractPaymentMethod
{
    /**
     * Assigned key for this payment method.
     */
    protected string $key;

    /**
     * Logo collection of objects with urls.
     * Usually logo is same for all languages, but exceptions exist.
     * @var TranslationCollection<Translation>
     */
    protected TranslationCollection $logos;

    public function __construct(
        string $key,
        string $defaultLanguage = self::DEFAULT_LANGUAGE
    ) {
        parent::__construct();

        $this->key = $key;

        $this->logos = new TranslationCollection();
        $this->titleTranslations = new TranslationCollection();

        $this->setDefaultLanguage($defaultLanguage);
    }

    /**
     * Get assigned payment method key.
     */
    public function getKey(): string
    {
        return $this->key;
    }

    /**
     * Returns collection of objects with country codes (country code ISO2) and logo urls.
     * @return TranslationCollection<Translation>
     */
    public function getLogos(): TranslationCollection
    {
        return $this->logos;
    }

    /**
     * Gets logo url for this payment method. Uses specified language or default one.
     * If logotype is not found for specified language, null is returned.
     *
     * @param string|null $language [Optional] (country code ISO2)
     */
    public function getLogoUrl(string $language = null): ?string
    {
        return $this->translator->translate($this->logos, $this->getDefaultLanguage(), $language);
    }

    /**
     * Gets title for this payment method. Uses specified language or default one.
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
}
