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

    public static function getCRUDControllerClass(): string
    {
        return CRUDController::class;
    }

    public function getRequiredRole(): string
    {
        return 'ROLE_ADMIN';
    }

    public function getTemplate(string $context): string
    {
        return self::ROUTE_CONTEXT_UPDATE === $context ? '@EDBAdmin/update.html.twig' : '@EDBAdmin/list.html.twig';
    }

    public function getTemplateArguments(string $context, ?BaseEntity $entity = null): array
    {
        return [];
    }

    public static function showInMenu(): bool
    {
        return true;
    }

    /**
     * @return array<string, string[][]>
     */
    public static function getRouteConfiguration(): array
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

    public static function getAdminMenuGroup(): ?string
    {
        return null;
    }

    public static function getAdminMenuIcon(): ?string
    {
        return 'dot-circle';
    }

    public static function getAdminMenuOrderName(): ?string
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
