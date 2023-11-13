<?php

namespace Paysera\CheckoutSdk\Util;

class RedirectExecutor
{
    public function execute(string $url, string $output): void
    {
        headers_sent() ? $this->redirectByScript($url, $output) : $this->redirectByHeader($url, $output);
    }

    protected function redirectByHeader(string $url, string $output): void
    {
        header("Location: $url");
        exit($output);
    }

    protected function redirectByScript(string $url, string $output): void
    {
        exit('<script type="text/javascript">window.location = "' . addslashes($url) . '";</script>' . $output);
    }
}
