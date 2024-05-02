<?php

declare(strict_types=1);

namespace EDB\AdminBundle\Entity;

use DateTimeImmutable;
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
    protected ?int $id;

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
        $this->createdAt = new DateTimeImmutable();
        $this->updatedAt = new DateTimeImmutable();
    }

    #[PreUpdate]
    public function onPreUpdate()
    {
        $this->updatedAt = new DateTimeImmutable();
    }
}
