<?php

namespace EDB\AdminBundle\Twig;

use EDB\AdminBundle\Entity\AbstractMedia;
use EDB\AdminBundle\Service\MediaService;
use Psr\Log\LoggerInterface;
use Throwable;
use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;

class MediaExtension extends AbstractExtension
{
    public function __construct(
        protected MediaService $mediaService,
        protected string $mediaPath,
        protected string $sourcePrefix,
        protected string $cachePrefix,
        protected LoggerInterface $logger,
    ) {
    }

    public function getFunctions(): array
    {
        return [
            new TwigFunction('media_path', [$this, 'getMediaPath'], ['is_safe' => ['html']]),
            new TwigFunction('media_download', [$this, 'getDownloadUrl'], ['is_safe' => ['html']]),
            new TwigFunction('render_media', [$this, 'renderMedia'], ['is_safe' => ['html']])
        ];
    }

    public function renderMedia(?AbstractMedia $media, int $width, int $height, string $fit = "crop", array $options = []): ?string
    {
        if (empty($media)) return null;

        if ('crop' === $fit) {
            $fit .= sprintf('-%d-%d', $media->getFocusPoint()['x'], $media->getFocusPoint()['y']);
        }

        try {
            $imageUrl = $this->mediaService->makeImage($media->getFilename(), [
                'w' => $width,
                'h' => $height,
                'fit' => $fit,
                ...$options
            ]);

            return sprintf(
                '%s/%s',
                rtrim($this->mediaPath, '/'),
                $imageUrl
            );
        } catch (Throwable $throwable) {
            $this->logger->critical($throwable->getMessage());
            return null;
        }
    }

    /**
     * @deprecated Use media_download / getDownloadUrl instead.
     */
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

    public function getDownloadUrl(?AbstractMedia $media): ?string
    {
        if (empty($media)) return null;

        $this->mediaService->moveToPublicFilesystem($media);

        return sprintf(
            '%s/%s/%s',
            rtrim($this->mediaPath, '/'),
            trim($this->cachePrefix, '/'),
            $media->getOriginalFilename()
        );
    }
}
