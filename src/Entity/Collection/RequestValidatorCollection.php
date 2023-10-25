<?php

declare(strict_types=1);

namespace Paysera\CheckoutSdk\Entity\Collection;

use Paysera\CheckoutSdk\Entity\RequestInterface;
use Paysera\CheckoutSdk\Validator\RequestValidatorInterface;

/**
 * @method RequestValidatorCollection filter(callable $filterFunction)
 */
class RequestValidatorCollection extends Collection implements RequestValidatorInterface
{
    public function isCompatible(object $item): bool
    {
        return $item instanceof RequestValidatorInterface;
    }

    public function canValidate(RequestInterface $request): bool
    {
        $compatibleValidatorCollection = $this->filter(
            static fn (RequestValidatorInterface $validator) => $validator->canValidate($request)
        );

        return (bool) $compatibleValidatorCollection->count();
    }

    public function validate(RequestInterface $request): void
    {
        foreach ($this as $validator) {
            if ($validator->canValidate($request)) {
                $validator->validate($request);
            }
        }
    }

    public function current(): ?RequestValidatorInterface
    {
        return parent::current();
    }

    protected function getItemType(): string
    {
        return RequestValidatorInterface::class;
    }
}
