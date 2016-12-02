<?php

namespace TestApp\Loader;

use ReflectionClass;
use ReflectionMethod;
use Symfony\Component\Config\Loader\Loader;
use Symfony\Component\Routing\Route;
use Symfony\Component\Routing\RouteCollection;

/**
 * Add to services.yml Di config
 *
 * routing type: magic
 *
 * services:
 * some.routing_loader:
 * class: SomeBundle\Routing\Loader\MagicLoader
 * tags:
 * - { name: routing.loader }
 *
 * Class MagicLoader
 * @package TestApp\Loader
 */
class MagicLoader extends Loader
{
    /**
     * Postfix values
     *
     * @const string
     */
    const BUNDLE_POSTFIX = 'Bundle';
    const CONTROLLER_POSTFIX = 'Controller';
    const ACTION_POSTFIX = 'Action';

    /**
     * Controller class name
     *
     * @var string
     */
    protected $controllerClassName;

    /**
     * {@inheritdoc}
     *
     * @todo send $class instead $resource through another loader
     * @todo like \Symfony\Component\Routing\Loader\AnnotationDirectoryLoader
     */
    public function load($controllerClassName, $type = null): RouteCollection
    {
        // @TODO make it through $resource -> parse $files -> parse $class
        if (!class_exists($controllerClassName)) {
            throw new MagicLoaderException('Invalid controller class name');
        }

        $this->controllerClassName = $controllerClassName;

        $routes = new RouteCollection();

        $methods = (new ReflectionClass($this->controllerClassName))
            ->getMethods(ReflectionMethod::IS_PUBLIC);

        foreach ($methods as $method) {
            // @TODO fix fooActionBar like cases
            $markerPosition = strpos($method->name, self::ACTION_POSTFIX);

            if ($markerPosition === false
                || $markerPosition != strlen($method->name) - strlen(self::ACTION_POSTFIX)
            ) {
                continue;
            }

            $action = substr($method->name, 0, - strlen(self::ACTION_POSTFIX));

            $routes->add(
                $action,
                new Route(
                    $this->getRoutePath($action),
                    $this->getRouteParameters($action)
                )
            );
        }

        return $routes;
    }

    /**
     * Get bundle name
     *
     * @return string
     */
    protected function getBundleName(): string
    {
        $explodedName = explode('\\', $this->controllerClassName);
        $bundleIndex = count($explodedName) - 3;

        return substr(
            explode('\\', $this->controllerClassName)[$bundleIndex],
            0,
            - strlen(self::BUNDLE_POSTFIX)
        );
    }

    /**
     * Get raw bundle name
     *
     * @return string
     */
    protected function getRawBundleName(): string
    {
        $explodedName = explode('\\', $this->controllerClassName);
        $bundleIndex = count($explodedName) - 3;

        return $explodedName[$bundleIndex];
    }

    /**
     * Get controller name
     *
     * @return string
     */
    protected function getControllerName(): string
    {
        $explodedName = explode('\\', $this->controllerClassName);
        $controllerIndex = count($explodedName) - 1;

        return substr(
            $explodedName[$controllerIndex],
            0,
            - strlen(self::CONTROLLER_POSTFIX)
        );
    }

    /**
     * Get route path
     *
     * @param string $action Action name
     * @return string
     */
    protected function getRoutePath(string $action): string
    {
        return strtolower($this->getBundleName())
            . '/' . strtolower($this->getControllerName())
            . '/' . $action;
    }

    /**
     * Get route parameters
     *
     * @todo find or add helper
     * @param string $action Action name
     * @return array
     */
    protected function getRouteParameters(string $action): array
    {
        return [
            '_controller' => $this->getRawBundleName()
                . ':' . ucfirst($this->getControllerName())
                . ':' . $action,
        ];
    }

    /**
     * Supports
     *
     * @param mixed $resource Resource
     * @param mixed $type Type
     * @return bool
     */
    public function supports($resource, $type = null): bool
    {
        return 'magic' === $type;
    }
}