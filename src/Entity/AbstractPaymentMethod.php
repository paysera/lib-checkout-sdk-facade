<?php

declare(strict_types=1);

namespace Paysera\CheckoutSdk\Entity;

use Paysera\CheckoutSdk\Entity\Collection\ItemInterface;
use Paysera\CheckoutSdk\Entity\Collection\TranslationCollection;
use Paysera\CheckoutSdk\Service\Translator;

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

    protected Translator $translator;

    public function __construct()
    {
        $this->translator = new Translator();
    }

    /**
     * Sets default language for translations.
     * Returns itself for fluent interface.
     * @param string $language
     * @return static
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
}
