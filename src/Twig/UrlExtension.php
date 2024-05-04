<?php

declare(strict_types=1);

namespace EDB\AdminBundle\Twig;

use EDB\AdminBundle\Helper\AdminUrlHelper;
use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;

class UrlExtension extends AbstractExtension
{
    public function __construct(
        protected AdminUrlHelper $adminUrlHelper,
    ) {
    }

    public function getFunctions(): array
    {
        return [
            new TwigFunction('generate_admin_url', [$this->adminUrlHelper, 'generateAdminUrl'])
        ];
    }
}
