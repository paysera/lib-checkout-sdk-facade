<?php

declare(strict_types=1);

namespace Paysera\CheckoutSdk\Provider\WebToPay\Helper;

class RedirectToPaymentHelper
{
    public function catchOutputBuffer(callable $function): string
    {
        ob_start();
        $function();
        return (string) (ob_get_clean());
    }

    /**
     * @return array<int, string>
     */
    public function getResponseHeaders(): array
    {
        return headers_list();
    }

    public function removeResponseHeader(string $header): void
    {
        header_remove(ucfirst(trim($header)));
    }
}
