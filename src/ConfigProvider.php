<?php

namespace Paysera\CheckoutSdk;

use Paysera\CheckoutSdk\Entity\Collection\RequestValidatorCollection;
use Paysera\CheckoutSdk\Provider\ProviderInterface;
use Paysera\CheckoutSdk\Provider\WebToPay\WebToPayProvider;
use Paysera\CheckoutSdk\Validator\RequestValidatorInterface;

class ConfigProvider
{
    private array $config;

    public function __construct()
    {
        $this->config = [
            'container' => [
                'aliases' => [
                    ProviderInterface::class => WebToPayProvider::class,
                    RequestValidatorInterface::class => RequestValidatorCollection::class,
                ],
            ],
        ];
    }

    public function get(string $id)
    {
        $keys = explode('.', $id);

        return $this->getNested($keys, $this->config);
    }

    private function getNested(array $keys, array $config)
    {
        $key = array_shift($keys);

        if ($key === null) {
            return null;
        }

        $value = $config[$key] ?? null;

        if (count($keys) === 0) {
            return $value;
        }

        if (is_array($value) === false) {
            return null;
        }

        return $this->getNested($keys, $value);
    }
}
