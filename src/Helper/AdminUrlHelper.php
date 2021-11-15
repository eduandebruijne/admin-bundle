<?php

namespace EDB\AdminBundle\Helper;

use EDB\AdminBundle\Admin\AbstractAdmin;
use EDB\AdminBundle\Util\ClassUtils;
use Exception;
use Symfony\Component\Routing\RouterInterface;

class AdminUrlHelper
{
    private RouterInterface $router;

    public function __construct(RouterInterface $router)
    {
        $this->router = $router;
    }

    public function generateAdminUrl(string $class, string $action, ?int $id = null): string
    {
        $shortName = ClassUtils::getShortName($class);
        $params = [];

        if (in_array($action, [AbstractAdmin::ROUTE_CONTEXT_UPDATE, AbstractAdmin::ROUTE_CONTEXT_DELETE])) {
            if (!$id) throw new Exception("Object 'id' is mandatory for route context '$action'");
            $params = ['id' => $id];
        }

        return $this->router->generate(sprintf('%s_%s', $shortName, $action), $params);
    }
}