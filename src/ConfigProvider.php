<?php

namespace Paysera\CheckoutSdk;

use Paysera\CheckoutSdk\Factory\RequestValidatorFactory;
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
                ],
                'factories' => [
                    RequestValidatorInterface::class => static fn ($container) => (new RequestValidatorFactory())->create($container),
                ],
            ],
        ];
    }

    public function get(string $id = null)
    {
        if ($id === null) {
            return $this->config;
        }

        $keys = explode('.', $id);

        $currentLevelConfig = $this->config;
        foreach ($keys as $key) {
            if (!is_array($currentLevelConfig) || !array_key_exists($key, $currentLevelConfig)) {
                return null;
            }
            $currentLevelConfig = $currentLevelConfig[$key];
        }
        return $currentLevelConfig;
    }
}
