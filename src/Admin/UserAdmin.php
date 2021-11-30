<?php

declare(strict_types=1);

namespace EDB\AdminBundle\Admin;

use EDB\AdminBundle\Admin\AbstractAdmin;
use EDB\AdminBundle\Entity\User;
use EDB\AdminBundle\FormBuilder\FormCollection;
use EDB\AdminBundle\ListBuilder\ListCollection;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\Extension\Core\Type\TextType;

class UserAdmin extends AbstractAdmin
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

    public static function getEntityClass(): string
    {
        return User::class;
    }

    public static function getAdminMenuTitle(): string
    {
        return 'Users';
    }
}