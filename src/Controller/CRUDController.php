<?php

declare(strict_types=1);

namespace EDB\AdminBundle\Controller;

use Doctrine\ORM\QueryBuilder;
use EDB\AdminBundle\Admin\AdminInterface;
use EDB\AdminBundle\Admin\Pool as AdminPool;
use EDB\AdminBundle\Entity\BaseEntity;
use EDB\AdminBundle\Entity\EntityHierarchyInterface;
use EDB\AdminBundle\Entity\SortableEntityInterface;
use EDB\AdminBundle\FormBuilder\Dynamic;
use EDB\AdminBundle\FormBuilder\FormCollection;
use EDB\AdminBundle\ListBuilder\ListCollection;
use EDB\AdminBundle\Util\ClassUtils;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\EntityNotFoundException;
use EDB\AdminBundle\Admin\AbstractAdmin;
use EDB\AdminBundle\Helper\AdminUrlHelper;
use Exception;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Core\Security;
use Twig\Environment;

class CRUDController
{
    public function __construct(
        protected Environment $twig,
        protected AdminPool $adminPool,
        protected FormFactoryInterface $formFactory,
        protected EntityManagerInterface $entityManager,
        protected AdminUrlHelper $adminUrlHelper,
        protected Security $security
    ) {
    }

    public function dashboard(): Response
    {
        if(!$this->security->isGranted('ROLE_ADMIN')) throw new Exception('Access denied.');

        return new Response($this->twig->render('@EDBAdmin/layout.html.twig'));
    }

    public function list(Request $request): Response
    {
        $admin = $this->getAdminFromRequest($request);
        $hierarchyEnabled = is_subclass_of(
            $admin->getEntityClass(),
            EntityHierarchyInterface::class
        );

        if(!$this->security->isGranted($admin->getRequiredRoleForRoute(AbstractAdmin::ROUTE_CONTEXT_LIST))) {
            throw new Exception('Access denied.');
        }

        $listCollection = new ListCollection();
        $admin->buildList($listCollection);

        $rootAlias = 'o';
        $queryBuilder = $this->entityManager->getRepository($admin->getEntityClass())->createQueryBuilder($rootAlias);
        $queryBuilder->select($rootAlias);

        $associationMappings = $this->entityManager->getClassMetadata($admin->getEntityClass())->getAssociationMappings();
        $allColumns = $listCollection->getColumns();

        foreach ($allColumns as $column) {
            $columnName = $column->getName();
            $parts = explode('.', $columnName);
            $field = sprintf('%s.%s', $rootAlias, $parts[0]);
            if (in_array($parts[0], array_keys($associationMappings))) {
                $alias = ClassUtils::getShortName($associationMappings[$columnName]["targetEntity"]) . '_' . $columnName;
                $queryBuilder->leftJoin($field, $alias);
                $queryBuilder->addSelect($alias);
            }
        }

        $sort = $request->query->get('sort');
        if ($sort) {
            $direction = false !== strpos($sort, '!') ? 'DESC' : 'ASC';
            $cleanedUpSortField = str_replace('!', '', $sort);

            if (false === strpos($sort, '.')) {
                $cleanedUpSortField = sprintf('%s.%s', $rootAlias, $cleanedUpSortField);
            }

            $queryBuilder->orderBy($cleanedUpSortField, $direction);
        }

        $admin->extendQuery($queryBuilder);

        $search = $request->query->get('search');
        if ($search) {
            $likeValue = $search;
            $field = $admin->getSearchProperty();
            $whereValues = [
                implode("", ['%', $likeValue, '%']),
                implode("", ['%', $likeValue]),
                implode("", [$likeValue, '%'])
            ];
            foreach ($whereValues as $id => $whereValue) {
                $parameterName = sprintf(':%s_%s', $field, $id);
                $queryBuilder
                    ->orWhere(
                        sprintf('%s LIKE %s', sprintf('%s.%s', $rootAlias, $field), $parameterName)
                    )
                    ->setParameter($parameterName, $whereValue);
            }
        }

        if ($hierarchyEnabled) {
            $listResults = [];
            $this->addResultSetToListResults(
                $listResults,
                $this->executePartialListQuery($queryBuilder, $rootAlias, null),
                $queryBuilder,
                $rootAlias
            );
        } else {
            $listResults = $queryBuilder->getQuery()->getResult();
        }

        $crudContext = AbstractAdmin::ROUTE_CONTEXT_LIST;
        $templateArguments = [
            'list' => $listResults,
            'list_collection' => $listCollection,
            'admin' => $admin,
            'sort' => $sort,
            'search' => $search
        ] + $admin->getTemplateArguments($crudContext);

        return new Response(
            $this->twig->render($admin->getTemplate(AbstractAdmin::ROUTE_CONTEXT_LIST), $templateArguments)
        );
    }

    private function addResultSetToListResults(
        &$listResults,
        array $resultSet,
        QueryBuilder $queryBuilder,
        string $rootAlias
    ): void {
        foreach ($resultSet as $result) {
            $listResults[] = $result;

            $this->addResultSetToListResults(
                $listResults,
                $this->executePartialListQuery(
                    $queryBuilder,
                    $rootAlias,
                    $result->getId()
                ),
                $queryBuilder,
                $rootAlias
            );
        }
    }

    protected function executePartialListQuery(QueryBuilder $queryBuilder, string $rootAlias, ?int $parentId): array
    {
        $qbClone = clone $queryBuilder;

        if (null === $parentId) {
            $qbClone->andWhere(sprintf('%s.parent IS NULL', $rootAlias));
        } else {
            $qbClone->andWhere(sprintf('%s.parent = :parentId', $rootAlias));
            $qbClone->setParameter('parentId', $parentId);
        }

        return $qbClone->getQuery()->getResult();
    }

    public function create(Request $request)
    {
        $admin = $this->getAdminFromRequest($request);

        if(!$this->security->isGranted($admin->getRequiredRoleForRoute(AbstractAdmin::ROUTE_CONTEXT_CREATE))) {
            throw new Exception('Access denied.');
        }

        $class = $admin->getEntityClass();
        $form = $this->buildForm($admin, new $class());
        $adminListUrl = $this->adminUrlHelper->generateAdminUrl($class, AbstractAdmin::ROUTE_CONTEXT_LIST);

        if ($request->isMethod('POST')) {
            $form->handleRequest($request);

            if ($form->isValid()) {
                $object = $form->getData();

                $admin->prePersist($object);
                $this->entityManager->persist($object);
                $admin->preFlush($object);
                $this->entityManager->flush();

                return new RedirectResponse($adminListUrl);
            }
        }

        $crudContext = AbstractAdmin::ROUTE_CONTEXT_CREATE;
        $templateArguments = [
            'form' => $form->createView(),
            'back' => $adminListUrl
        ] + $admin->getTemplateArguments($crudContext);

        return new Response(
            $this->twig->render($admin->getTemplate($crudContext), $templateArguments)
        );
    }

    public function update(Request $request)
    {
        $admin = $this->getAdminFromRequest($request);

        if(!$this->security->isGranted($admin->getRequiredRoleForRoute(AbstractAdmin::ROUTE_CONTEXT_UPDATE))) {
            throw new Exception('Access denied.');
        }

        $object = $this->getObjectByRequest($admin, $request);
        $adminListUrl = $this->adminUrlHelper->generateAdminUrl($admin->getEntityClass(), AbstractAdmin::ROUTE_CONTEXT_LIST);

        if (empty($object)) {
            throw new EntityNotFoundException();
        }

        $form = $this->buildForm($admin, $object);

        if ($request->isMethod('POST')) {
            $form->handleRequest($request);

            if ($form->isValid()) {
                $object = $form->getData();

                $admin->preUpdate($object);
                $admin->preFlush($object);
                $this->entityManager->flush();

                return new RedirectResponse($adminListUrl);
            }
        }

        $crudContext = AbstractAdmin::ROUTE_CONTEXT_UPDATE;
        $templateArguments = [
            'form' => $form->createView(),
            'back' => $adminListUrl,
            'object' => $object
        ] + $admin->getTemplateArguments($crudContext, $object);

        return new Response(
            $this->twig->render($admin->getTemplate(AbstractAdmin::ROUTE_CONTEXT_UPDATE), $templateArguments)
        );
    }

    public function moveDown(Request $request)
    {
        $admin = $this->getAdminFromRequest($request);
        if(!$this->security->isGranted($admin->getRequiredRoleForRoute(AbstractAdmin::ROUTE_CONTEXT_MOVE_DOWN))) {
            throw new Exception('Access denied.');
        }

        return $this->sort($request, function($position) { return $position + 1; });
    }

    public function moveUp(Request $request)
    {
        $admin = $this->getAdminFromRequest($request);
        if(!$this->security->isGranted($admin->getRequiredRoleForRoute(AbstractAdmin::ROUTE_CONTEXT_MOVE_UP))) {
            throw new Exception('Access denied.');
        }

        return $this->sort($request, function($position) { return $position - 1; });
    }

    private function sort(Request $request, callable $method)
    {
        $admin = $this->getAdminFromRequest($request);
        $hierarchyEnabled = is_subclass_of(
            $admin->getEntityClass(),
            EntityHierarchyInterface::class
        );

        $object = $this->getObjectByRequest($admin, $request);

        if (!$object instanceof SortableEntityInterface) {
            throw new Exception('Entity must extend SortableEntity');
        }

        $qb = $this->entityManager
            ->getRepository($admin->getEntityClass())
            ->createQueryBuilder('e')
            ->select('e')
            ->orderBy('e.position', 'ASC')
        ;

        if (true === $hierarchyEnabled) {
            if (null === $object->getParent()) {
                $qb->where('e.parent IS NULL');
            } else {
                $qb->where('e.parent = :parentLevel');
                $qb->setParameter('parentLevel', $object->getParent()->getId());
            }
        }

        $resultSet = $qb->getQuery()->getResult();

        foreach ($resultSet as $position => $instance) {
            if ($object === $instance && isset($resultSet[$method($position)])) {
                $resultSet[$position] = $resultSet[$method($position)];
                $resultSet[$method($position)] = $object;

                break;
            }
        }

        foreach ($resultSet as $position => $instance) {
            $instance->setPosition($position+1);
        }

        $this->entityManager->flush();

        return new RedirectResponse($this->adminUrlHelper->generateAdminUrl($admin->getEntityClass(), AbstractAdmin::ROUTE_CONTEXT_LIST));
    }

    public function delete(Request $request): RedirectResponse
    {
        $admin = $this->getAdminFromRequest($request);

        if(!$this->security->isGranted($admin->getRequiredRoleForRoute(AbstractAdmin::ROUTE_CONTEXT_DELETE))) {
            throw new Exception('Access denied.');
        }

        $object = $this->getObjectByRequest($admin, $request);
        $this->entityManager->remove($object);
        $this->entityManager->flush();

        return new RedirectResponse($this->adminUrlHelper->generateAdminUrl($admin->getEntityClass(), AbstractAdmin::ROUTE_CONTEXT_LIST));
    }

    private function getAdminFromRequest(Request $request): AdminInterface
    {
        return $this->adminPool->getAdminForClass($request->attributes->get('_entity'));
    }

    private function getObjectByRequest(AdminInterface $admin, Request $request): ?BaseEntity
    {
        $id = $request->attributes->getInt('id');
        $repository = $this->entityManager->getRepository($admin->getEntityClass());
        $object = $repository->find($id);

        if ($object instanceof BaseEntity) {
            return $object;
        }

        throw new Exception(sprintf('Object with id #%d not found!', $id));
    }

    protected function buildForm(AdminInterface $admin, BaseEntity $data): FormInterface
    {
        $formCollection = new FormCollection();
        $admin->buildForm($formCollection);

        $formUrl = (
            $data->getId() ?
            $this->adminUrlHelper->generateAdminUrl($admin->getEntityClass(), AbstractAdmin::ROUTE_CONTEXT_UPDATE, ['id' => $data->getId()]) :
            $this->adminUrlHelper->generateAdminUrl($admin->getEntityClass(), AbstractAdmin::ROUTE_CONTEXT_CREATE)
        );

        return $this->formFactory->create(Dynamic::class, $data, [
            'data_class' => $admin->getEntityClass(),
            'form_collection' => $formCollection,
            'action' => $formUrl,
            'method' => 'POST'
        ]);
    }
}
