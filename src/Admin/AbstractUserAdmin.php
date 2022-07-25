<?php

declare(strict_types=1);

namespace EDB\AdminBundle\Admin;

use EDB\AdminBundle\Entity\BaseEntity;
use EDB\AdminBundle\FormBuilder\FormCollection;
use EDB\AdminBundle\ListBuilder\ListCollection;
use EDB\AdminBundle\Util\StringUtils;
use League\OAuth2\Client\Grant\Password;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

abstract class AbstractUserAdmin extends AbstractAdmin
{
    private UserPasswordHasherInterface $passwordHasher;

    public function __construct(UserPasswordHasherInterface $passwordHasher)
    {
        $this->passwordHasher = $passwordHasher;
    }

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
            ->add('plainPassword', PasswordType::class)
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

    public function preFlush(BaseEntity $entity)
    {
        if ($entity->plainPassword) {
            $entity->setSalt(StringUtils::generateRandomString());
            $entity->setPassword($this->passwordHasher->hashPassword($entity, $entity->plainPassword));
        }
    }

    abstract public function getEntityClass(): string;

    public function getAdminMenuTitle(): string
    {
        return 'Users';
    }
}
