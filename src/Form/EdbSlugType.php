<?php

namespace EDB\AdminBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextType;

class EdbSlugType extends AbstractType
{
    public function getParent(): string
    {
        return TextType::class;
    }
}
