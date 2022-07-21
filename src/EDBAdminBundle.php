<?php

namespace EDB\AdminBundle;

use EDB\AdminBundle\DependencyInjection\EDBAdminCompilerPass;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\HttpKernel\Bundle\Bundle;

class EDBAdminBundle extends Bundle
{
    public function build(ContainerBuilder $container)
    {
        parent::build($container);
        $container->addCompilerPass(new EDBAdminCompilerPass());
    }
}
