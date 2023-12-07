<?php

declare(strict_types=1);

namespace Paysera\CheckoutSdk\Tests\Util;

use Iterator;
use Paysera\CheckoutSdk\Exception\BaseException;
use Paysera\CheckoutSdk\Exception\ContainerException;
use Paysera\CheckoutSdk\Exception\ContainerNotFoundException;
use Paysera\CheckoutSdk\Tests\AbstractCase;
use Paysera\CheckoutSdk\Tests\Fixtures\ClassWithNotTypedArgument;
use Paysera\CheckoutSdk\Tests\Fixtures\ClassWithNotTypedArgumentHasDefaultValue;
use Paysera\CheckoutSdk\Util\Invader;

class ContainerTest extends AbstractCase
{
    public function testHasNotId(): void
    {
        $this->assertFalse($this->container->has(Invader::class));
    }

    public function testHasId(): void
    {
        $this->container->get(Invader::class);

        $this->assertTrue($this->container->has(Invader::class));
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

    public function testGetBuildException(): void
    {
        $this->expectException(ContainerNotFoundException::class);
        $this->expectExceptionMessage('Service with id `' . ClassWithNotTypedArgument::class . '` not found in container');

        $this->container->get(ClassWithNotTypedArgument::class);
    }

    public function testBuildSimpleObject(): void
    {
        $this->assertFalse($this->container->has(Invader::class));

        $this->assertNotSame(
            $this->container->get(Invader::class),
            $this->container->build(Invader::class)
        );
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

    public function testBuildWithConstructorArgumentsWithoutType(): void
    {
        $this->expectException(ContainerException::class);
        $this->expectExceptionMessage('Can not resolve class dependency input');

        $this->container->build(ClassWithNotTypedArgument::class);
    }

    public function testBuildWithConstructorArgumentsWithoutTypeHasDefaultValue(): void
    {
        /** @var ClassWithNotTypedArgumentHasDefaultValue $object */
        $object = $this->container->build(ClassWithNotTypedArgumentHasDefaultValue::class);

        $this->assertEquals(['test'], $object->array, 'Object property must be set by default value.');
    }
}
