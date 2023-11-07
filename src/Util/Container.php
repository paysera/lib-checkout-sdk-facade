<?php

namespace Paysera\CheckoutSdk\Util;

use Exception;
use Paysera\CheckoutSdk\ConfigProvider;
use Paysera\CheckoutSdk\Exception\ContainerException;
use Paysera\CheckoutSdk\Exception\ContainerNotFoundException;
use Psr\Container\ContainerExceptionInterface;
use Psr\Container\ContainerInterface;
use Psr\Container\NotFoundExceptionInterface;
use ReflectionClass;
use ReflectionException;

class Container implements ContainerInterface
{
    protected array $instances;
    protected ConfigProvider $configProvider;

    public function __construct(ConfigProvider $configProvider = null)
    {
        $this->instances = [];
        $this->configProvider = $configProvider ?? new ConfigProvider();
        $this->instances[ConfigProvider::class] = $this->configProvider;
    }

    public function has(string $id): bool
    {
        return isset($this->instances[$id]);
    }

    public function get(string $id): object
    {
        if ($this->has($id) === false) {
            try {
                $instance = $this->build($id);
            } catch (Exception $exception) {
                throw new ContainerNotFoundException("Service with id `$id` not found in container.");
            }

            $this->set($id, $instance);
        }

        return $this->instances[$id];
    }

    public function set(string $id, object $concrete = null): void
    {
        $this->instances[$id] = $concrete;
    }

    /**
     * @param string $id
     * @return object
     * @throws ContainerException
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     */
    public function build(string $id): object
    {
        $aliases = $this->configProvider->get('container.aliases');
        $alias = $aliases[$id] ?? null;
        if ($alias !== null) {
            return $this->get($alias);
        }

        $factories = $this->configProvider->get('container.factories');
        $factory = $factories[$id] ?? null;
        if (is_callable($factory)) {
            return $factory($this);
        }

        try {
            $instance = $this->createInstance($id);
        } catch (ReflectionException $exception) {
            throw new ContainerException("Class $id has instantiable issues.");
        }

        return $instance;
    }

    /**
     * @param string $className
     * @return object
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     * @throws ReflectionException
     */
    protected function createInstance(string $className): object
    {
        $reflector = new ReflectionClass($className);

        if (!$reflector->isInstantiable()) {
            throw new ContainerException("Class $className is not instantiable");
        }

        $constructor = $reflector->getConstructor();
        if ($constructor === null) {
            return $reflector->newInstance();
        }

        $constructorParameters = $constructor->getParameters();
        $dependencies = $this->getDependencies($constructorParameters);

        return $reflector->newInstanceArgs($dependencies);
    }

    /**
     * @param array $constructorParameters
     * @return array
     * @throws ContainerException
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     */
    protected function getDependencies(array $constructorParameters): array
    {
        $dependencies = [];
        foreach ($constructorParameters as $constructorParameter) {
            $dependency = $constructorParameter->getClass();
            if ($dependency === null) {
                if ($constructorParameter->isDefaultValueAvailable()) {
                    $dependencies[] = $constructorParameter->getDefaultValue();
                } else {
                    throw new ContainerException("Can not resolve class dependency $constructorParameter->name");
                }
            } else {
                $dependencies[] = $this->get($dependency->name);
            }
        }

        return $dependencies;
    }
}
