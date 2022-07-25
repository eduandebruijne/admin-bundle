<?php

declare(strict_types=1);

namespace EDB\AdminBundle\Admin;

use EDB\AdminBundle\FormBuilder\FormCollection;
use EDB\AdminBundle\ListBuilder\ListCollection;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\Extension\Core\Type\TextType;

abstract class AbstractUserAdmin extends AbstractAdmin
{
    public function buildList(ListCollection $collection)
    {
        $collection->add('username')->addActions([
            'update' => [],
            'delete' => [],
        ]);
    }

    public function buildForm(FormCollection $collection)
    {
        $collection
            ->add('username', TextType::class)
            ->add('roles', CollectionType::class, [
                'allow_add' => true,
                'allow_delete' => true,
                'entry_type' => TextType::class,
                'entry_options' => [
                    'label' => false,
                    'attr' => [
                        'class' => 'form-control-sm'
                    ]
                ],
            ])
        ;
    }

    abstract public function getEntityClass(): string;

    public function getAdminMenuTitle(): string
    {
        return 'Users';
    }
}
