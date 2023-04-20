<?php

namespace EDB\AdminBundle\Twig;

use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;
use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;

class ParameterExtension extends AbstractExtension
{
    public function __construct(
        protected ParameterBagInterface $params
    ) {
    }

    public function getFunctions()
    {
        return [
            new TwigFunction('get_parameter', [$this, 'getParameter'])
        ];
    }

    public function getParameter($parameter)
    {
        return $this->params->get($parameter);
    }
}
