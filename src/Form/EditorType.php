<?php

namespace EDB\AdminBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;

class EditorType extends AbstractType
{
    public function getParent()
    {
        return HiddenType::class;
    }
}