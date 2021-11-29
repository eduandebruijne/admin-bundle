<?php

namespace EDB\AdminBundle\Service;

use Doctrine\ORM\EntityManagerInterface;
use EDB\AdminBundle\Entity\Media;
use EDB\AdminBundle\Util\StringUtils;
use League\Flysystem\FilesystemOperator;
use League\Glide\Server;
use League\Glide\ServerFactory;
use Symfony\Component\HttpFoundation\Request;

class MediaService
{
    private Server $server;
    private FilesystemOperator $filesystem;
    private EntityManagerInterface $entityManager;
    private string $sourcePrefix;
    private string $cachePrefix;

    public function __construct(
        FilesystemOperator $defaultFilesystem,
        EntityManagerInterface $entityManager,
        string $sourcePrefix,
        string $cachePrefix
    )
    {
        $this->filesystem = $defaultFilesystem;
        $this->entityManager = $entityManager;
        $this->sourcePrefix = $sourcePrefix;
        $this->cachePrefix = $cachePrefix;

        $this->server = ServerFactory::create([
            'source' => $this->filesystem,
            'source_path_prefix' => $this->sourcePrefix,
            'cache' => $this->filesystem,
            'cache_path_prefix' => $this->cachePrefix,
            'group_cache_in_folders' => true,
            'watermarks' => $this->filesystem,
            'watermarks_path_prefix' => 'watermarks',
            'driver' => 'gd',
        ]);
    }

    public function createFromRequest(Request $request, ?string $field = null): ?Media
    {
        if (count($request->files->all()) < 1) return null;
        if (!$field) {
            $uploadedFile = current(current($request->files->all()));
        } else {
            $uploadedFile = $request->files->get($field);
        }

        $filename = $uploadedFile->getClientOriginalName();
        $mimetype = $uploadedFile->getClientMimeType();
        $size = $uploadedFile->getSize();
        $extension = $uploadedFile->getClientOriginalExtension();

        $newFilename = StringUtils::generateRandomString();
        $this->filesystem->write(
            sprintf('%s/%s', 'source', $newFilename),
            file_get_contents($uploadedFile->getRealPath())
        );

        $media = new Media();
        $media->setTitle($filename);
        $media->setFilename($newFilename);
        $media->setExtension($extension);
        $media->setMimeType($mimetype);
        $media->setSize($size);

        $this->entityManager->persist($media);
        $this->entityManager->flush();

        return $media;
    }

    public function getImageAsBase64($filename, ...$args) {
        return $this->server->getImageAsBase64($filename, ...$args);
    }

    public function makeImage($filename, ...$args) {
        return $this->server->makeImage($filename, ...$args);
    }
}