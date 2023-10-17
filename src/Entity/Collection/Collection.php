<?php

declare(strict_types=1);

namespace Paysera\CheckoutSdk\Entity\Collection;

use BadMethodCallException;
use Countable;
use Iterator;
use Paysera\CheckoutSdk\Exception\CheckoutIntegrationException;

abstract class Collection implements Iterator, Countable
{
    private int $position;

    private array $array;

    public function __construct(array $array = [])
    {
        $this->position = 0;
        $this->exchangeArray($array);
    }

    abstract public function isCompatible(object $item): bool;

    protected function appendToCollection($value): void
    {
        if ($this->isCompatible($value) === false) {
            CheckoutIntegrationException::throwInvalidType(static::class);
        }

        $this->array[] = $value;
    }

    protected function setToCollection($key, $value): void
    {
        if ($this->isCompatible($value) === false) {
            CheckoutIntegrationException::throwInvalidType(static::class);
        }

        $this->array[$key] = $value;
    }

    public function count(): int
    {
        return count($this->array);
    }

    public function current(): object
    {
        return $this->array[$this->position];
    }

    public function next(): void
    {
        ++$this->position;
    }

    public function key(): int
    {
        return $this->position;
    }

    public function valid(): bool
    {
        return isset($this->array[$this->position]);
    }

    public function rewind(): void
    {
        $this->position = 0;
    }

    /**
     * Returns new collection instance with filtered items.
     */
    public function filter(callable $filterFunction): Collection
    {
        $filteredArray = array_filter($this->array, $filterFunction);

        return new static(array_values($filteredArray));
    }

    public function exchangeArray(array $array): void
    {
        $isCompatible = array_reduce($array, fn($carry, $item) => $carry && $this->isCompatible($item), true);

        if ($isCompatible === false) {
            CheckoutIntegrationException::throwInvalidType(static::class);
        }

        $this->rewind();
        $this->array = $array;
    }

    protected function getByGetter($needle, string $getterName): ?object
    {
        if ($this->count() === 0) {
            return null;
        }

        $item = array_values($this->array)[0];
        if (method_exists($item, $getterName)) {
            $filteredArray = array_filter(
                $this->array,
                static fn (object $item) => $item->{$getterName}() === $needle
            );

            return array_values($filteredArray)[0] ?? null;
        }

        $className = get_class($item);
        throw new BadMethodCallException("Method `$getterName()` does not exist in class `$className`");
    }
}
