<?php

namespace Paysera\CheckoutSdk\Entity;

use Paysera\CheckoutSdk\Util\RedirectExecutor;

class PaymentRedirectResponse
{
    protected string $redirectOutput;
    protected string $redirectUrl;
    protected RedirectExecutor $redirectExecutor;

    public function __construct(string $redirectUrl)
    {
        $this->redirectExecutor = new RedirectExecutor();
        $this->redirectUrl = $redirectUrl;
        $this->redirectOutput = $this->getDefaultRedirectOutput();
    }

    public function makeRedirect(): void
    {
        $this->redirectExecutor->execute($this->redirectUrl, $this->redirectOutput);
    }

    public function getRedirectOutput(): string
    {
        return $this->redirectOutput;
    }

    public function setRedirectOutput(string $redirectOutput): void
    {
        $this->redirectOutput = $redirectOutput;
    }

    public function getRedirectUrl(): string
    {
        return $this->redirectUrl;
    }

    protected function getDefaultRedirectOutput(): string
    {
        return sprintf(
            'Redirecting to <a href="%s">%s</a>. Please wait.',
            htmlentities($this->redirectUrl, ENT_QUOTES, 'UTF-8'),
            htmlentities($this->redirectUrl, ENT_QUOTES, 'UTF-8')
        );
    }
}
