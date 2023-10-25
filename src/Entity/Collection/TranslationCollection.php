<?php

declare(strict_types=1);

namespace Paysera\CheckoutSdk\Entity\Collection;

use Paysera\CheckoutSdk\Entity\Translation;

/**
 * @method TranslationCollection filter(callable $filterFunction)
 */
class TranslationCollection extends Collection
{
    public function isCompatible(object $item): bool
    {
        return $item instanceof Translation;
    }

    public function getByLanguage(string $language): ?Translation
    {
        return $this->filter(
            static fn (Translation $translation) => $translation->getLanguage()
            === $language
        )->current();
    }

    public function append(Translation $value): void
    {
        $this->appendToCollection($value);
    }

    public function current(): ?Translation
    {
        return parent::current();
    }

    protected function getItemType(): string
    {
        return Translation::class;
    }
}
