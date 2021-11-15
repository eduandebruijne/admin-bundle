<?php

namespace EDB\AdminBundle\Twig;

use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;
use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;

class ParameterExtension extends AbstractExtension
{
    protected $params; 

    public function getFunctions()
    {
        return [
            new TwigFunction('get_parameter', array($this, 'getParameter'))
        ];
    }
    
    public function __construct(ParameterBagInterface $params)
    {
        $this->params = $params;
    }     

    public function getParameter($parameter)
    {
        return $this->params->get($parameter);
    }
}