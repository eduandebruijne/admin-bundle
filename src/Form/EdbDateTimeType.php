<?php

declare(strict_types=1);

namespace EDB\AdminBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\DateTimeType;
use Symfony\Component\OptionsResolver\OptionsResolver;

class EdbDateTimeType extends AbstractType
{
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefault('widget', 'single_text');
    }

    public function getParent()
    {
        return DateTimeType::class;
    }
}
