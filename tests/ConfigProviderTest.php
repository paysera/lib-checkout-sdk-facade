<?php

namespace Paysera\CheckoutSdk\Tests;

use Paysera\CheckoutSdk\ConfigProvider;

class ConfigProviderTest extends AbstractCase
{
    public function testGetWithoutKey(): void
    {
        $configProvider = new ConfigProvider();
        $configProviderProperties = $this->getObjectProperties($configProvider);

        $this->assertEquals($configProviderProperties['config'], $configProvider->get());
    }

    /**
     * @dataProvider getWithKeyDataProvider
     */
    public function testGetWithKey(string $key, $expected): void
    {
        $configProvider = new ConfigProvider();

        $this->assertEquals($expected, $configProvider->get($key));
    }

    public function getWithKeyDataProvider(): array
    {
        return [
            'Return value by existed key' => [
                'container',
                (new ConfigProvider())->get()['container']
            ],
            'Return value by existed combined key' => [
                'container.aliases',
                (new ConfigProvider())->get()['container']['aliases']
            ],
            'Return null by absent key' => [
                'undefinedKey',
                null,
            ],
        ];
    }
}
