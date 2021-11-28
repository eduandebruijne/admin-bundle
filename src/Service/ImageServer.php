<?php

namespace EDB\AdminBundle\Service;

use League\Flysystem\FilesystemOperator;
use League\Glide\Server;
use League\Glide\ServerFactory;

class ImageServer
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

    public function writeToPrivate(...$args)
    {
        $this->filesystem->write(...$args);
    }

    public function getImageAsBase64($filename, ...$args) {
        return $this->server->getImageAsBase64($filename, ...$args);
    }

    public function makeImage($filename, ...$args) {
        return $this->server->makeImage($filename, ...$args);
    }
}