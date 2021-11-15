<?php

namespace EDB\AdminBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextType;

class QuillType extends AbstractType
{
    public function getParent()
    {
        return TextType::class;
    }
}