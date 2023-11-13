<?php

namespace Paysera\CheckoutSdk\Provider\WebToPay\Helper;

class RedirectToPaymentHelper
{
    public function catchOutputBuffer(callable $function): string
    {
        ob_start();
        $function();
        return ob_get_clean();
    }

    public function getResponseHeaders(): array
    {
        return headers_list();
    }

    public function removeResponseHeader(string $header): void
    {
        header_remove(ucfirst(trim($header)));
    }
}
