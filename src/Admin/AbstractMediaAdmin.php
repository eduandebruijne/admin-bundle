<?php

declare(strict_types=1);

namespace EDB\AdminBundle\Admin;

use EDB\AdminBundle\FormBuilder\FormCollection;
use EDB\AdminBundle\ListBuilder\ListCollection;
use EDB\AdminBundle\Service\MediaService;
use Symfony\Component\Form\CallbackTransformer;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\TextType;

abstract class AbstractMediaAdmin extends AbstractAdmin
{
    private MediaService $mediaService;

    public function __construct(MediaService $mediaService)
    {
        $this->mediaService = $mediaService;
    }

    public function buildList(ListCollection $collection)
    {
        $collection
            ->add('title')
            ->add('mimeType')
            ->add('extension')
            ->add('filename')
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
            ->add('update', FileType::class)
            ->setModelTransformer(new CallbackTransformer(function($database) {
                return $database;
            }, function($form) {
                if (!$form->update) return $form;

                $media = $this->mediaService->handleUploadedFile($form->update);
                $form->setExtension($media->getExtension());
                $form->setMimeType($media->getMimeType());
                $form->setFilename($media->getFilename());
                $form->setSize($media->getSize());

                return $form;
            }));
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
}
