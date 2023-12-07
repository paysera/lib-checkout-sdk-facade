<?php

declare(strict_types=1);

namespace Paysera\CheckoutSdk\Tests\Fixtures;

class ClassWithNotTypedArgument
{
    public array $array;

    public function __construct($input)
    {
        $this->array = $input;
    }
}
