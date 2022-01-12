<?php

declare(strict_types=1);

namespace EDB\AdminBundle\Admin;

use EDB\AdminBundle\Admin\AbstractAdmin;
use EDB\AdminBundle\Entity\Media;
use EDB\AdminBundle\FormBuilder\FormCollection;
use EDB\AdminBundle\ListBuilder\ListCollection;
use EDB\AdminBundle\Service\MediaService;
use Exception;
use Symfony\Component\Form\CallbackTransformer;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\TextType;

class MediaAdmin extends AbstractAdmin
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
                /** @var Media $form */
                if (!$form->update) return $form;

                $media = $this->mediaService->handleUploadedFile($form->update);
                $form->setExtension($media->getExtension());
                $form->setMimeType($media->getMimeType());
                $form->setFilename($media->getFilename());
                $form->setSize($media->getSize());

                return $form;
            }));
    }

    public static function getEntityClass(): string
    {
        throw new Exception('Create and extend this admin to use it for your own Media class.');
    }

    public static function getAdminMenuTitle(): string
    {
        return 'Media';
    }
}