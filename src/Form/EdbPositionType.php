<?php

declare(strict_types=1);

namespace EDB\AdminBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\OptionsResolver\OptionsResolver;

class EdbPositionType extends AbstractType
{
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(
            [
                'required' => false,
                'empty_data' => 0,
                'attr' => [
                    'class' => 'edb_admin_position',
                    'autocomplete' => 'off'
                ],
            ]
        );
    }

    public function getParent()
    {
        return HiddenType::class;
    }
}
