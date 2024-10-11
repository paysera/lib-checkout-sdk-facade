<?php

declare(strict_types=1);

namespace Paysera\CheckoutSdk\Entity;

class PaymentRedirectResponse
{
    protected string $redirectUrl;
    protected string $data;

    public function __construct(string $redirectUrl, string $data)
    {
        $this->redirectUrl = $redirectUrl;
        $this->data = $data;
    }

    public function getRedirectUrl(): string
    {
        return $this->redirectUrl;
    }

    public function getData(): string
    {
        return $this->data;
    }
}
