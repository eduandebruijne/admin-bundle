<?php

namespace EDB\AdminBundle\Twig;

use EDB\AdminBundle\Entity\AbstractMedia;
use EDB\AdminBundle\Service\MediaService;
use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;

class MediaExtension extends AbstractExtension
{
    private MediaService $mediaService;
    private string $mediaPath;
    private string $sourcePrefix;

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
            new TwigFunction('render_media', [$this, 'renderMedia'], ['is_safe' => ['html']]),
        ];
    }

    private function isImage(string $mimeType): bool
    {
        return in_array($mimeType, [
            'image/bmp',
            'image/jpeg',
            'image/jpg',
            'image/png',
        ]);
    }

    public function renderMedia(?AbstractMedia $media, int $width, int $height, string $fit = "crop")
    {
        if (empty($media)) return null;

        if ($this->isImage($media->getMimeType())) {
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
        }

        return $this->getFileUploadThumbnail();
    }

    public function getMediaPath(?AbstractMedia $media)
    {
        if (empty($media)) return null;

        return sprintf(
            '%s/%s/%s',
            rtrim($this->mediaPath, '/'),
            trim($this->sourcePrefix, '/'),
            $media->getFilename()
        );
    }

    private function getFileUploadThumbnail()
    {
        return "
        data:image/png;base64,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
        ";
    }
}
