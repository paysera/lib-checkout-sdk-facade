<?php

namespace Paysera\CheckoutSdk\Entity;

use Paysera\CheckoutSdk\Entity\Collection\TranslationCollection;

abstract class AbstractPaymentMethod
{
    protected const DEFAULT_LANGUAGE = 'lt';

    /**
     * Collection of translation objects.
     */
    protected TranslationCollection $titleTranslations;

    /**
     * Default language (compatible with country code ISO2 or 'en') for translations.
     */
    protected string $defaultLanguage;

    /**
     * Sets default language for translations.
     * Returns itself for fluent interface.
     */
    public function setDefaultLanguage(string $language): self
    {
        $this->defaultLanguage = strtolower($language);

        return $this;
    }

    /**
     * Gets default language for translations.
     */
    public function getDefaultLanguage(): string
    {
        return $this->defaultLanguage;
    }

    /**
     * Returns collection of translation objects.
     */
    public function getTitleTranslations(): TranslationCollection
    {
        return $this->titleTranslations;
    }

    /**
     * Get translation from collection. Uses specified language or default one.
     * Can return the default value if any languages were not specified.
     */
    protected function translate(
        TranslationCollection $translationCollection,
        ?string $language,
        string $defaultLanguage,
        ?string $defaultValue
    ): ?string {
        if ($language !== null) {
            $translation = $translationCollection->getByLanguage($language);
            if ($translation !== null) {
                return $translation->getValue();
            }
        }

        $defaultTranslation = $translationCollection->getByLanguage($defaultLanguage);
        if ($defaultTranslation !== null) {
            return $defaultTranslation->getValue();
        }

        return $defaultValue;
    }
}
