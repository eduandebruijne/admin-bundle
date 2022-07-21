<?php

declare(strict_types=1);

namespace EDB\AdminBundle\Route;

use EDB\AdminBundle\Admin\AdminInterface;
use EDB\AdminBundle\Admin\Pool;
use Symfony\Component\Config\Loader\Loader as BaseLoader;
use Symfony\Component\Config\Loader\LoaderInterface;
use Symfony\Component\Config\Loader\LoaderResolverInterface;
use Symfony\Component\Routing\Route;
use Symfony\Component\Routing\RouteCollection;
use function sprintf;
use function Symfony\Component\String\u;

class Loader extends BaseLoader implements LoaderInterface
{
    private bool $loaded = false;
    private Pool $pool;

    /**
     * @param Pool $pool
     */
    public function __construct(Pool $pool)
    {
        parent::__construct();
        $this->pool = $pool;
    }

    public function load($resource, string $type = null): RouteCollection
    {
        if ($this->loaded === true) {
            throw new \RuntimeException('Do not add this loader twice');
        }

        $routes = new RouteCollection();
        foreach ($this->pool->getAdmins() as $admin) {
            /** @var AdminInterface $admin */
            foreach ($admin->getRouteConfiguration() as $context => $routeConfig) {
                $methods = $routeConfig['methods'] ?? [];
                $params = $routeConfig['params'] ?? [];

                $paramsString = implode('/', array_map(function ($param) {
                    return sprintf('{%s}', $param);
                }, $params));

                if (!empty($paramsString)) {
                    $paramsString = sprintf('%s/', $paramsString);
                }

                $fullUrl = sprintf('%s/%s%s', $admin->getPluralClassName(), $paramsString, $context);
                $camelContext = u($context)->camel();
                $adminRoute = new Route($fullUrl, [
                    '_controller' => sprintf('%s::%s', $admin::getCRUDControllerClass(), $camelContext),
                    '_entity' => $admin::getEntityClass(),
                ], $params, [], '', [], $methods);

                $path = $admin->getPath($context);
                $routes->add(
                    $path,
                    $adminRoute
                );
            }
        }

        $this->addDashboardRoute($routes);

        return $routes;
    }

    public function supports($resource, string $type = null): bool
    {
        return 'admin' === $type;
    }

    public function getResolver()
    {
    }

    public function setResolver(LoaderResolverInterface $resolver)
    {
    }

    private function addDashboardRoute(RouteCollection $routes): void
    {
        $route = new Route(
            '',
            ['_controller' => 'EDB\AdminBundle\Controller\CRUDController::dashboard'],
            [],
            [],
            '',
            [],
            ['GET']
        );
        $routes->add('dashboard', $route);
    }
}
