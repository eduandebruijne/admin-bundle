<?php

namespace EDB\AdminBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\HttpFoundation\File\UploadedFile;

/**
 * @ORM\MappedSuperclass
 */
class AbstractMedia extends BaseEntity
{
    /**
     * @ORM\Column
     */
    private ?string $title;

    /**
     * @ORM\Column
     */
    private ?string $filename;

    /**
     * @ORM\Column(nullable=true)
     */
    private ?string $mimeType;

    /**
     * @ORM\Column(nullable=true)
     */
    private ?string $extension;

    /**
     * @ORM\Column(type="integer", nullable=true)
     */
    private ?int $size;

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
}
