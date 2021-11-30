<?php

namespace EDB\AdminBundle\Service;

use EDB\AdminBundle\Entity\Media;
use EDB\AdminBundle\Util\StringUtils;
use League\Flysystem\FilesystemOperator;
use League\Glide\Server;
use League\Glide\ServerFactory;
use Symfony\Component\HttpFoundation\File\UploadedFile;

class MediaService
{
    private Server $server;
    private FilesystemOperator $filesystem;
    private string $sourcePrefix;
    private string $cachePrefix;

    public function __construct(
        FilesystemOperator $defaultFilesystem,
        string $sourcePrefix,
        string $cachePrefix
    )
    {
        $this->filesystem = $defaultFilesystem;
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

    public function handleUploadedFile(UploadedFile $uploadedFile): ?Media
    {
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

        return $media;
    }

    public function getImageAsBase64($filename, ...$args) {
        return $this->server->getImageAsBase64($filename, ...$args);
    }

    public function makeImage($filename, ...$args) {
        return $this->server->makeImage($filename, ...$args);
    }
}