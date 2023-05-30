<?php

namespace EDB\AdminBundle\Twig;

use EDB\AdminBundle\Entity\AbstractMedia;
use EDB\AdminBundle\Service\MediaService;
use Throwable;
use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;

class MediaExtension extends AbstractExtension
{
    protected MediaService $mediaService;
    protected string $mediaPath;
    protected string $sourcePrefix;

    public function __construct(MediaService $mediaService, string $mediaPath, string $sourcePrefix)
    {
        $this->mediaService = $mediaService;
        $this->mediaPath = $mediaPath;
        $this->sourcePrefix = $sourcePrefix;
    }

    public function getFunctions(): array
    {
        return [
            new TwigFunction('media_path', [$this, 'getMediaPath'], ['is_safe' => ['html']]),
            new TwigFunction('render_media', [$this, 'renderMedia'], ['is_safe' => ['html']])
        ];
    }

    public function renderMedia(?AbstractMedia $media, int $width, int $height, string $fit = "crop"): ?string
    {
        if (empty($media)) return null;

        if ('crop' === $fit) {
            $fit .= sprintf('-%d-%d', $media->getFocusPoint()['x'], $media->getFocusPoint()['y']);
        }

        try {
            $imageUrl = $this->mediaService->makeImage($media->getFilename(), [
                'w' => $width,
                'h' => $height,
                'fit' => $fit
            ]);

            return sprintf(
                '%s/%s',
                rtrim($this->mediaPath, '/'),
                $imageUrl
            );
        } catch (Throwable $throwable) {
            return null;
        }
    }

    public function getMediaPath(?AbstractMedia $media): ?string
    {
        if (empty($media)) return null;

        return sprintf(
            '%s/%s/%s',
            rtrim($this->mediaPath, '/'),
            trim($this->sourcePrefix, '/'),
            $media->getFilename()
        );
    }
}
