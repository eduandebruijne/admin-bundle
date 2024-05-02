<?php

namespace EDB\AdminBundle\Entity;

use Doctrine\ORM\Mapping\Column;
use Doctrine\ORM\Mapping\MappedSuperclass;

#[MappedSuperclass]
class AbstractMedia extends BaseEntity
{
    #[Column(type: 'string')]
    protected ?string $title;

    #[Column(type: 'string')]
    protected ?string $filename;

    #[Column(type: 'string')]
    protected ?string $originalFilename;

    #[Column(type: 'string', nullable: true)]
    protected ?string $mimeType;

    #[Column(type: 'string', nullable: true)]
    protected ?string $extension;

    #[Column(type: 'integer', nullable: true)]
    protected ?int $size;

    #[Column(type: 'json', nullable: true)]
    protected array $focusPoint;

    public $update;

    public function __toString()
    {
        return $this->title ?? '#';
    }

    public function getTitle(): ?string
    {
        return $this->title;
    }

    public function setTitle(string $title)
    {
        $this->title = $title;
    }

    public function getFilename(): ?string
    {
        return $this->filename;
    }

    public function setFilename(?string $filename): void
    {
        $this->filename = $filename;
    }

    public function getOriginalFilename(): ?string
    {
        return $this->originalFilename;
    }

    public function setOriginalFilename(?string $originalFilename): void
    {
        $this->originalFilename = $originalFilename;
    }

    public function getMimeType(): ?string
    {
        return $this->mimeType;
    }

    public function setMimeType(?string $mimeType): void
    {
        $this->mimeType = $mimeType;
    }

    public function getExtension(): ?string
    {
        return $this->extension;
    }

    public function setExtension(?string $extension): void
    {
        $this->extension = $extension;
    }

    public function getSize(): ?int
    {
        return $this->size;
    }

    public function setSize(?int $size): void
    {
        $this->size = $size;
    }

    public function getFocusPoint(): array
    {
        if (empty($this->focusPoint) || null === $this->focusPoint['x']) {
            return ['x' => 50, 'y' => 50];
        }

        return $this->focusPoint;
    }

    public function setFocusPoint(array $focusPoint): void
    {
        $this->focusPoint = $focusPoint;
    }
}
