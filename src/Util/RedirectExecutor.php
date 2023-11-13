<?php

namespace Paysera\CheckoutSdk\Util;

class RedirectExecutor
{
    public function execute(string $redirectUrl, string $redirectOutput): void
    {
        if (headers_sent()) {
            print '<script type="text/javascript">window.location = "' . addslashes($redirectUrl) . '";</script>';
        } else {
            header("Location: $redirectUrl");
        }

        print $redirectOutput;
        exit();
    }
}
