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
        return $this->getByGetter($language, 'getLanguage');
    }

    public function append(Translation $value): void
    {
        $this->appendToCollection($value);
    }

    public function set($key, Translation $value): void
    {
        $this->setToCollection($key, $value);
    }

    public function current(): Translation
    {
        return parent::current();
    }
}
