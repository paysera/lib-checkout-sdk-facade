<?php

declare(strict_types=1);

namespace Paysera\CheckoutSdk\Tests\Fixtures;

class ClassWithNotTypedArgumentHasDefaultValue
{
    public array $array;

    public function __construct($input = ['test'])
    {
        $this->array = $input;
    }
}
