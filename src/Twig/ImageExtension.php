<?php

namespace EDB\AdminBundle\Twig;

use EDB\AdminBundle\Entity\Media;
use League\Glide\Server;
use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;

class ImageExtension extends AbstractExtension
{
    private Server $server;
    private string $mediaPath;

    public function __construct(Server $server, string $mediaPath)
    {
        $this->server = $server;
        $this->mediaPath = $mediaPath;
    }

    public function getFunctions(): array
    {
        return [
            new TwigFunction('get_media_base_64', [$this, 'getMediaBase64'], ['is_safe' => ['html']]),
            new TwigFunction('get_media_path', [$this, 'getMediaPath'], ['is_safe' => ['html']]),
        ];
    }

    public function getMediaBase64(?Media $media, int $width, int $height, string $fit = "crop")
    {
        if (empty($media)) return null;

        $this->server->getImageAsBase64($media->getFilename(), [
            'w' => $width,
            'h' => $height,
            'fit' => $fit
        ]);
    }

    public function getMediaPath(?Media $media, int $width, int $height, string $fit = "crop")
    {
        if (empty($media)) return null;

        $imageUrl = $this->server->makeImage($media->getFilename(), [
            'w' => $width,
            'h' => $height,
            'fit' => $fit
        ]);

        return sprintf('%s/%s', rtrim($this->mediaPath, '/'), $imageUrl);
    }
}