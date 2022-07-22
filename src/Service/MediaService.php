<?php

namespace EDB\AdminBundle\Service;

use EDB\AdminBundle\Entity\AbstractMedia;
use EDB\AdminBundle\Util\StringUtils;
use Exception;
use League\Flysystem\FilesystemOperator;
use League\Glide\Server;
use League\Glide\ServerFactory;
use Symfony\Component\HttpFoundation\File\UploadedFile;

class MediaService
{
    private Server $server;
    private FilesystemOperator $publicFilesystem;
    private FilesystemOperator $privateFilesystem;
    private string $sourcePrefix;
    private string $cachePrefix;
    private string $mediaClass;

    public function __construct(
        FilesystemOperator $defaultFilesystem,
        string $sourcePrefix,
        string $cachePrefix,
        ?string $mediaClass,
        ?FilesystemOperator $privateFilesystem = null
    )
    {
        if (!$mediaClass) {
            throw new Exception('No media class is implemented in this project.');
        }

        $this->publicFilesystem = $defaultFilesystem;
        $this->privateFilesystem = $privateFilesystem ?? $defaultFilesystem;

        $this->sourcePrefix = $sourcePrefix;
        $this->cachePrefix = $cachePrefix;
        $this->mediaClass = $mediaClass;

        $this->server = ServerFactory::create([
            'source' => $this->privateFilesystem,
            'source_path_prefix' => $this->sourcePrefix,
            'cache' => $this->publicFilesystem,
            'cache_path_prefix' => $this->cachePrefix,
            'group_cache_in_folders' => false,
            'watermarks' => $this->privateFilesystem,
            'watermarks_path_prefix' => 'watermarks',
            'driver' => 'gd',
        ]);
    }

    public function handleUploadedFile(UploadedFile $uploadedFile): ?AbstractMedia
    {
        $filename = $uploadedFile->getClientOriginalName();
        $mimetype = $uploadedFile->getClientMimeType();
        $size = $uploadedFile->getSize();
        $extension = $uploadedFile->getClientOriginalExtension();

        $newFilename = StringUtils::generateRandomString();
        $this->privateFilesystem->write(
            sprintf('%s/%s', $this->sourcePrefix, $newFilename),
            file_get_contents($uploadedFile->getRealPath())
        );

        $media = new $this->mediaClass();
        $media->setTitle($filename);
        $media->setFilename($newFilename);
        $media->setExtension($extension);
        $media->setMimeType($mimetype);
        $media->setSize($size);

        return $media;
    }

    public function makeImage($filename, ...$args) {
        return $this->server->makeImage($filename, ...$args);
    }
}
