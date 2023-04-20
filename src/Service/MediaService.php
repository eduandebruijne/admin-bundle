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
    public function __construct(
        private FilesystemOperator $defaultFilesystem,
        private string $sourcePrefix,
        private string $cachePrefix,
        private ?string $mediaClass,
        private ?FilesystemOperator $privateFilesystem = null
    ) {
        if (!$this->privateFilesystem) {
            $this->privateFilesystem = $this->publicFilesystem;
        }

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

    private function checkMediaClass()
    {
        if (null === $this->mediaClass) {
            throw new Exception('No media class defined for project.');
        }
    }

    public function handleUploadedFile(UploadedFile $uploadedFile): ?AbstractMedia
    {
        $this->checkMediaClass();

        $originalFilename = $uploadedFile->getClientOriginalName();
        $mimetype = $uploadedFile->getClientMimeType();
        $size = $uploadedFile->getSize();
        $extension = $uploadedFile->getClientOriginalExtension();
        $title = str_replace(sprintf('.%s', $extension), '',  $originalFilename);

        $newFilename = StringUtils::generateRandomString();
        $this->privateFilesystem->write(
            sprintf('%s/%s', $this->sourcePrefix, $newFilename),
            file_get_contents($uploadedFile->getRealPath())
        );

        $media = new $this->mediaClass();
        $media->setTitle($title);
        $media->setFilename($newFilename);
        $media->setOriginalFilename($originalFilename);
        $media->setExtension($extension);
        $media->setMimeType($mimetype);
        $media->setSize($size);

        return $media;
    }

    public function makeImage($filename, ...$args) {
        $this->checkMediaClass();

        return $this->server->makeImage($filename, ...$args);
    }
}
