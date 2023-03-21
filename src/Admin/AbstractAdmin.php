<?php

declare(strict_types=1);

namespace EDB\AdminBundle\Admin;

use EDB\AdminBundle\Controller\CRUDController;
use EDB\AdminBundle\Entity\BaseEntity;
use EDB\AdminBundle\Util\ClassUtils;
use Doctrine\ORM\QueryBuilder;
use function sprintf;

abstract class AbstractAdmin implements AdminInterface
{
    public const ROUTE_CONTEXT_LIST = 'list';
    public const ROUTE_CONTEXT_CREATE = 'create';
    public const ROUTE_CONTEXT_UPDATE = 'update';
    public const ROUTE_CONTEXT_DELETE = 'delete';
    public const ROUTE_CONTEXT_MOVE_UP = 'move_up';
    public const ROUTE_CONTEXT_MOVE_DOWN = 'move_down';

    public function getCRUDControllerClass(): string
    {
        return CRUDController::class;
    }

    public function getRequiredRole(): string
    {
        return 'ROLE_ADMIN';
    }

    public function getTemplate(string $context): string
    {
        if (in_array($context, [
            self::ROUTE_CONTEXT_CREATE,
            self::ROUTE_CONTEXT_UPDATE
        ])) {
            return '@EDBAdmin/update.html.twig';
        }

        return '@EDBAdmin/list.html.twig';
    }

    public function getTemplateArguments(string $context, ?BaseEntity $entity = null): array
    {
        return [];
    }

    public function showInMenu(): bool
    {
        return true;
    }

    public function getSearchProperty(): string
    {
        return 'title';
    }

    /**
     * @return array<string, string[][]>
     */
    public function getRouteConfiguration(): array
    {
        return [
            self::ROUTE_CONTEXT_LIST => [
                'methods' => [
                    'GET',
                ],
            ],
            self::ROUTE_CONTEXT_CREATE => [
                'methods' => [
                    'GET',
                    'POST',
                ],
            ],
            self::ROUTE_CONTEXT_UPDATE => [
                'methods' => [
                    'GET',
                    'POST',
                ],
                'params' => [
                    'id',
                ],
            ],
            self::ROUTE_CONTEXT_MOVE_UP => [
                'methods' => [
                    'GET',
                ],
                'params' => [
                    'id',
                ],
            ],
            self::ROUTE_CONTEXT_MOVE_DOWN => [
                'methods' => [
                    'GET',
                ],
                'params' => [
                    'id',
                ],
            ],
            self::ROUTE_CONTEXT_DELETE => [
                'methods' => [
                    'GET',
                    'DELETE',
                ],
                'params' => [
                    'id',
                ],
            ],
        ];
    }

    public function extendQuery(QueryBuilder $queryBuilder)
    {
    }

    public function preFlush(BaseEntity $entity)
    {
    }

    public function prePersist(BaseEntity $entity)
    {
    }

    public function preUpdate(BaseEntity $entity)
    {
    }

    public function getAdminMenuGroup(): ?string
    {
        return null;
    }

    public function getAdminMenuIcon(): ?string
    {
        return 'dot-circle';
    }

    public function getAdminMenuOrderName(): ?string
    {
        return null;
    }

    public function getPath(string $context): string
    {
        $pluralType = ClassUtils::getShortName($this->getEntityClass());

        return sprintf('%s_%s', $pluralType, $context);
    }

    public function getPluralClassName(): string
    {
        $pluralType = ClassUtils::getShortName($this->getEntityClass());

        return ClassUtils::pluralize($pluralType);
    }
}
