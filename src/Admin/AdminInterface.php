<?php

declare(strict_types=1);

namespace EDB\AdminBundle\Admin;

use EDB\AdminBundle\Entity\BaseEntity;
use EDB\AdminBundle\FormBuilder\FormCollection;
use EDB\AdminBundle\ListBuilder\ListCollection;
use Doctrine\ORM\QueryBuilder;

interface AdminInterface
{
    public function buildForm(FormCollection $collection);
    public function buildList(ListCollection $collection);
    public function extendQuery(QueryBuilder $queryBuilder);
    public function getPath(string $context): string;
    public function getPluralClassName(): string;
    public function getRequiredRole(): string;
    public function getTemplate(string $context): string;
    public function getTemplateArguments(string $context, ?BaseEntity $entity = null): array;
    public function preFlush(BaseEntity $entity);
    public function prePersist(BaseEntity $entity);
    public function preUpdate(BaseEntity $entity);
    public static function getAdminMenuGroup(): ?string;
    public static function getAdminMenuIcon(): ?string;
    public static function getAdminMenuOrderName(): ?string;
    public static function getAdminMenuTitle(): string;
    public static function getCRUDControllerClass(): string;
    public static function getEntityClass(): string;
    public static function getRouteConfiguration(): array;
}
