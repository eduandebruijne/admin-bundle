<?php

declare(strict_types=1);

namespace EDB\AdminBundle\Entity;

use DateTime;
use DateTimeInterface;
use Doctrine\ORM\Mapping\Column;
use Doctrine\ORM\Mapping\GeneratedValue;
use Doctrine\ORM\Mapping\Id;
use Doctrine\ORM\Mapping\MappedSuperclass;
use Doctrine\ORM\Mapping\PrePersist;
use Doctrine\ORM\Mapping\PreUpdate;

/**
 * IMPORTANT: Make sure to add #[MappedSuperclass] when extending this class
 */
#[MappedSuperclass]
abstract class BaseEntity
{
    #[Id]
    #[GeneratedValue]
    #[Column(type: 'integer')]
    protected ?int $id = null;

    #[Column(type: 'datetime')]
    protected ?DateTimeInterface $createdAt;

    #[Column(type: 'datetime')]
    protected ?DateTimeInterface $updatedAt;

    public function getId(): ?int
	{
		return $this->id;
	}

    #[PrePersist]
    public function onPrePersist()
    {
        $this->createdAt = new DateTime();
        $this->updatedAt = new DateTime();
    }

    #[PreUpdate]
    public function onPreUpdate()
    {
        $this->updatedAt = new DateTime();
    }

    public function getCreatedAt(): ?DateTimeInterface
    {
        return $this->createdAt;
    }

    public function getUpdatedAt(): ?DateTimeInterface
    {
        return $this->updatedAt;
    }
}
