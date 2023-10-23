<?php

declare(strict_types=1);

namespace Paysera\CheckoutSdk\Util;

use Closure;

class Invader
{
    /**
     * This function can extract all properties (even private and protected) of given object.
     */
    public static function getProperties(object $object): array
    {
        $invader = static function ($object) {
            return get_object_vars($object);
        };
        $invader = Closure::bind($invader, null, $object);

        return $invader($object);
    }
}
