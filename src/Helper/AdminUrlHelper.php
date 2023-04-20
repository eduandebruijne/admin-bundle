<?php

namespace EDB\AdminBundle\Helper;

use EDB\AdminBundle\Util\ClassUtils;
use Symfony\Component\Routing\RouterInterface;

class AdminUrlHelper
{
    public function __construct(
        private RouterInterface $router
    ) {
    }

    public function generateAdminUrl(string $class, string $action, array $params = []): string
    {
        $shortName = ClassUtils::getShortName($class);

        return $this->router->generate(sprintf('%s_%s', $shortName, $action), $params);
    }
}
