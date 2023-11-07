<?php

namespace Paysera\CheckoutSdk\Tests\Util;

use ArrayObject;
use Iterator;
use Paysera\CheckoutSdk\CheckoutFacade;
use Paysera\CheckoutSdk\ConfigProvider;
use Paysera\CheckoutSdk\Exception\BaseException;
use Paysera\CheckoutSdk\Exception\ContainerException;
use Paysera\CheckoutSdk\Exception\ContainerNotFoundException;
use Paysera\CheckoutSdk\Provider\ProviderInterface;
use Paysera\CheckoutSdk\Tests\AbstractCase;
use Paysera\CheckoutSdk\Util\Invader;
use Paysera\CheckoutSdk\Validator\RequestValidatorInterface;

class ContainerTest extends AbstractCase
{
    /**
     * @dataProvider hasDataProvider
     */
    public function testHas(string $id, bool $expected): void
    {
        $this->assertEquals($expected, $this->container->has($id));
    }

    public function testSet(): void
    {
        $this->assertFalse($this->container->has(Invader::class));

        $this->container->set(Invader::class, new Invader());

        $this->assertTrue($this->container->has(Invader::class));
    }

    public function testGet(): void
    {
        $this->assertFalse($this->container->has(Invader::class));
        $this->assertInstanceOf(Invader::class, $this->container->get(Invader::class));
        $this->assertTrue($this->container->has(Invader::class));
    }

    public function testBuildSimpleObject(): void
    {
        $this->assertFalse($this->container->has(Invader::class));

        $this->assertNotSame(
            $this->container->get(Invader::class),
            $this->container->build(Invader::class)
        );
    }

    public function testBuildAliasedObject(): void
    {
        /** @var ConfigProvider $configProvider */
        $configProvider = $this->container->get(ConfigProvider::class);
        $providerClass = $configProvider->get('container.aliases.' . ProviderInterface::class);

        $this->assertInstanceOf($providerClass, $this->container->build(ProviderInterface::class));
    }

    public function testBuildClassDoesNotExistException(): void
    {
        $this->expectException(ContainerException::class);
        $this->expectExceptionCode(BaseException::E_CONTAINER);

        $this->container->build('test');
    }

    public function testBuildClassIsNotInstantiableException(): void
    {
        $this->expectException(ContainerException::class);
        $this->expectExceptionCode(BaseException::E_CONTAINER);

        $this->container->build(Iterator::class);
    }

    public function testBuildWithFactory(): void
    {
        /** @var ConfigProvider $configProvider */
        $configProvider = $this->container->get(ConfigProvider::class);
        $factories = $configProvider->get('container.factories') ?? [];

        $this->assertArrayHasKey(RequestValidatorInterface::class, $factories);

        $validator = $this->container->build(RequestValidatorInterface::class);

        $this->assertInstanceOf(RequestValidatorInterface::class, $validator);
    }

    public function hasDataProvider(): array
    {
        return [
            'Return true by existent id' => [
                ConfigProvider::class,
                true,
            ],
            'Return false by absent id' => [
                CheckoutFacade::class,
                false,
            ],
        ];
    }
}
