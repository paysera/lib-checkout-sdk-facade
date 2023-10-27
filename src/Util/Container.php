<?php

namespace Paysera\CheckoutSdk\Util;

use Paysera\CheckoutSdk\ConfigProvider;
use Paysera\CheckoutSdk\Entity\Collection\Collection;
use Paysera\CheckoutSdk\Exception\ContainerException;
use Paysera\CheckoutSdk\Exception\ContainerNotFoundException;
use Paysera\CheckoutSdk\Exception\InvalidTypeException;
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
        $this->configProvider = $configProvider ?? new ConfigProvider();
        $this->instances[ConfigProvider::class] = $this->configProvider;
        $this->instances = [];
    }

    public function has(string $id): bool
    {
        return isset($this->instances[$id]);
    }

    public function get(string $id, array $parameters = []): object
    {
        if ($this->has($id) === false) {
            $this->set($id, $this->build($id, $parameters));
        }

        $result = $this->instances[$id] ?? null;

        if ($result === null) {
            throw new ContainerNotFoundException("Service with id `$id` not found in container.");
        }

        return $result;
    }

    public function set(string $id, object $concrete = null): void
    {
        $this->instances[$id] = $concrete;
    }

    /**
     * @param string $id
     * @param array $parameters
     * @return object
     * @throws ContainerException
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     */
    public function build(string $id, array $parameters = []): object
    {
        $aliases = $this->configProvider->get('container.aliases');
        $alias = $aliases[$id] ?? null;

        if ($alias !== null) {
            return $this->get($alias);
        }

        try {
            $instance = $this->createInstance($id);
        } catch (ReflectionException $exception) {
            throw new ContainerException("Class $id has instantiable issues.");
        }

        if ($instance instanceof Collection) {
            $this->fillCollectionWithInterfaces($instance);
        }

        return $instance;
    }

    /**
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     * @param Collection $collection
     */
    protected function fillCollectionWithInterfaces(Collection $collection): void
    {
        $itemType = $collection->getItemType();

        if (preg_match('/^.+Interface$/', $itemType) === false) {
            return;
        }

        $items = [];
        $collectionClassName = get_class($collection);
        foreach (get_declared_classes() as $className) {
            if ($className !== $collectionClassName && in_array($itemType, class_implements($className), true)) {
                $items[] = $this->get($className);
            }
        }

        try {
            $collection->exchangeArray($items);
        } catch (InvalidTypeException $exception) {
            throw new ContainerException("Cannot fill collection $collectionClassName.");
        }
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
