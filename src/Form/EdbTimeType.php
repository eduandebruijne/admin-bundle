<?php

declare(strict_types=1);

namespace EDB\AdminBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TimeType;
use Symfony\Component\OptionsResolver\OptionsResolver;

class EdbTimeType extends AbstractType
{
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefault('widget', 'single_text');
    }

    public function getParent()
    {
        return TimeType::class;
    }
}
