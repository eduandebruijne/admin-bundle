<?php

namespace EDB\AdminBundle\Helper;

use EDB\AdminBundle\Admin\AbstractAdmin;
use EDB\AdminBundle\Util\ClassUtils;
use Exception;
use Symfony\Component\Routing\RouterInterface;

class AdminUrlHelper
{
    public function __construct(
        protected RouterInterface $router,
    ) {
    }

    public function generateAdminUrl(string $class, string $action, array $params = []): string
    {
        $shortName = ClassUtils::getShortName($class);

        return $this->router->generate(sprintf('%s_%s', $shortName, $action), $params);
    }
}
