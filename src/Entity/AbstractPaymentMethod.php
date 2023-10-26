<?php

declare(strict_types=1);

namespace Paysera\CheckoutSdk\Entity;

use Paysera\CheckoutSdk\Entity\Collection\ItemInterface;
use Paysera\CheckoutSdk\Entity\Collection\TranslationCollection;

abstract class AbstractPaymentMethod implements ItemInterface
{
    protected const DEFAULT_LANGUAGE = 'lt';

    /**
     * Collection of translation objects.
     * @var TranslationCollection<Translation>
     */
    protected TranslationCollection $titleTranslations;

    /**
     * Default language (compatible with country code ISO2 or 'en') for translations.
     */
    protected string $defaultLanguage;

    /**
     * Sets default language for translations.
     * Returns itself for fluent interface.
     * @param string $language
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
     * @return TranslationCollection<Translation>
     */
    public function getTitleTranslations(): TranslationCollection
    {
        return $this->titleTranslations;
    }

    /**
     * Get translation from collection. Uses specified language or default one.
     * Can return the default value if any languages were not specified.
     * @param TranslationCollection<Translation> $translationCollection
     * @param ?string $language
     * @param string $defaultLanguage
     */
    protected function translate(
        TranslationCollection $translationCollection,
        ?string $language,
        string $defaultLanguage
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

        return null;
    }
}
