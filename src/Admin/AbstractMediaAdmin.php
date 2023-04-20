<?php

declare(strict_types=1);

namespace EDB\AdminBundle\Admin;

use Doctrine\ORM\EntityManagerInterface;
use EDB\AdminBundle\Entity\BaseEntity;
use EDB\AdminBundle\FormBuilder\FormCollection;
use EDB\AdminBundle\Form\EdbMediaFocusPointType;
use EDB\AdminBundle\ListBuilder\ListCollection;
use EDB\AdminBundle\Service\MediaService;
use Symfony\Component\Form\CallbackTransformer;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\HttpFoundation\RequestStack;

abstract class AbstractMediaAdmin extends AbstractAdmin
{
    public function __construct(
        protected MediaService $mediaService,
        protected EntityManagerInterface $entityManager,
        protected RequestStack $requestStack
    ) {
    }

    public function buildList(ListCollection $collection)
    {
        $collection
            ->add('title')
            ->add('mimeType')
            ->add('extension')
            ->add('originalFilename')
            ->add('size')
            ->addActions([
                'update' => [],
                'delete' => []
            ]);
    }

    public function buildForm(FormCollection $collection)
    {
        $collection
            ->add('title', TextType::class)
            ->add('mimeType', TextType::class, ['disabled' => true])
            ->add('extension', TextType::class, ['disabled' => true])
            ->add('filename', TextType::class, ['disabled' => true])
            ->add('size', TextType::class, ['disabled' => true])
            ->add('update', FileType::class, [
                'label' => 'Choose file'
            ])
            ->add('focusPoint', EdbMediaFocusPointType::class, [
                'mediaInstance' => $this->getObjectByRequest()
            ])
            ->setModelTransformer(new CallbackTransformer(function($database) {
                return $database;
            }, function($form) {
                if (!$form->update) return $form;

                $media = $this->mediaService->handleUploadedFile($form->update);
                $form->setExtension($media->getExtension());
                $form->setMimeType($media->getMimeType());
                $form->setFilename($media->getFilename());
                $form->setOriginalFilename($media->getOriginalFilename());
                $form->setSize($media->getSize());

                return $form;
            }));
    }

    public function preFlush(BaseEntity $entity)
    {
        // Because Symfony will serialize the user on every request,
        // make sure there is no UploadedFile still in the User object
        // when the User entity has a relationship with Media
        $entity->update = null;
    }

    abstract public function getEntityClass(): string;

    public function getPluralClassName(): string
    {
        return 'media';
    }

    public function getAdminMenuTitle(): string
    {
        return 'Media';
    }

    private function getObjectByRequest(): ?BaseEntity
    {
        try {
            $objectId = $this->requestStack->getCurrentRequest()->attributes->get('id');
            $object = $this->entityManager->getRepository($this->getEntityClass())->find($objectId);
        } catch (\Throwable $exception) {
            $object = null;
        }

        return $object;
    }
}
