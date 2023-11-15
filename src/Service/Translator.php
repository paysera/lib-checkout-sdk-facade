<?php

declare(strict_types=1);

namespace Paysera\CheckoutSdk\Service;

use Paysera\CheckoutSdk\Entity\Collection\TranslationCollection;
use Paysera\CheckoutSdk\Entity\Translation;

class Translator
{
    /**
     * Get translation from collection. Uses specified language or default one.
     * Can return the default value if any languages were not specified.
     * @param TranslationCollection<Translation> $collection
     * @param string $defaultLanguage
     * @param string|null $language
     * @return string|null
     */
    public function translate(
        TranslationCollection $collection,
        string $defaultLanguage,
        ?string $language = null
    ): ?string {
        $translation =
            $this->getTranslationByLanguage($collection, $language ?? $defaultLanguage)
            ?? $this->getTranslationByLanguage($collection, $defaultLanguage)
        ;

        return $translation !== null ? $translation->getValue() : null;
    }

    /**
     * @param TranslationCollection<Translation> $collection
     * @param string $language
     * @return Translation|null
     */
    protected function getTranslationByLanguage(
        TranslationCollection $collection,
        string $language
    ): ?Translation {
        return $collection->filter(
            static fn (Translation $translation) => $translation->getLanguage()
            === $language
        )->get();
    }
}
