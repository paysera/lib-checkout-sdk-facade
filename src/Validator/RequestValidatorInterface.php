<?php

declare(strict_types=1);

namespace Paysera\CheckoutSdk\Validator;

use Paysera\CheckoutSdk\Entity\Collection\ItemInterface;
use Paysera\CheckoutSdk\Entity\RequestInterface;

interface RequestValidatorInterface extends ItemInterface
{
    public function canValidate(RequestInterface $request): bool;

    public function validate(RequestInterface $request): void;
}
