<?php

declare(strict_types=1);

namespace EDB\AdminBundle\Controller;

use EDB\AdminBundle\Admin\AdminInterface;
use EDB\AdminBundle\Admin\Pool as AdminPool;
use EDB\AdminBundle\Entity\BaseEntity;
use EDB\AdminBundle\FormBuilder\Dynamic;
use EDB\AdminBundle\FormBuilder\FormCollection;
use EDB\AdminBundle\ListBuilder\ListCollection;
use EDB\AdminBundle\Util\ClassUtils;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\EntityNotFoundException;
use EDB\AdminBundle\Admin\AbstractAdmin;
use EDB\AdminBundle\Entity\SortableEntity;
use EDB\AdminBundle\Helper\AdminUrlHelper;
use Exception;
use ReflectionException;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Core\Security;
use Twig\Environment;
use Twig\Error\LoaderError;
use Twig\Error\RuntimeError;
use Twig\Error\SyntaxError;

class CRUDController
{
    protected Environment $twig;
    protected AdminPool $adminPool;
    protected FormFactoryInterface $formFactory;
    protected EntityManagerInterface $entityManager;
    protected AdminUrlHelper $adminUrlHelper;
    protected Security $security;

    public function __construct(
        Environment $twig,
        AdminPool $adminPool,
        FormFactoryInterface $formFactory,
        EntityManagerInterface $entityManager,
        AdminUrlHelper $adminUrlHelper,
        Security $security
    ) {
        $this->twig = $twig;
        $this->adminPool = $adminPool;
        $this->formFactory = $formFactory;
        $this->entityManager = $entityManager;
        $this->adminUrlHelper = $adminUrlHelper;
        $this->security = $security;
    }

    /**
     * @throws RuntimeError
     * @throws SyntaxError
     * @throws LoaderError
     */
    public function dashboard(): Response
    {
        if(!$this->security->isGranted('ROLE_ADMIN')) throw new Exception('Access denied.');

        return new Response($this->twig->render('@EDBAdmin/layout.html.twig'));
    }

    /**
     * @throws SyntaxError
     * @throws ReflectionException
     * @throws RuntimeError
     * @throws LoaderError
     * @throws Exception
     */
    public function list(Request $request): Response
    {
        $admin = $this->getAdminFromRequest($request);
        if(!$this->security->isGranted($admin->getRequiredRole())) throw new Exception('Access denied.');

        $listCollection = new ListCollection();
        $admin->buildList($listCollection);

        $rootAlias = 'o';
        $queryBuilder = $this->entityManager->getRepository($admin::getEntityClass())->createQueryBuilder($rootAlias);
        $queryBuilder->select($rootAlias);

        $associationMappings = $this->entityManager->getClassMetadata($admin::getEntityClass())->getAssociationMappings();
        $allColumns = $listCollection->getColumns();
        foreach ($allColumns as $column) {
            $columnName = $column->getName();
            $parts = explode('.', $columnName);
            $field = sprintf('%s.%s', $rootAlias, $parts[0]);
            if (in_array($parts[0], array_keys($associationMappings))) {
                $alias = ClassUtils::getShortName($associationMappings[$columnName]["targetEntity"]);
                $queryBuilder->leftJoin($field, $alias);
                $queryBuilder->addSelect($alias);
            }
        }

        $sort = $request->query->get('sort');
        if ($sort) {
            $direction = str_contains($sort, '!') ? 'DESC' : 'ASC';
            $cleanedUpSortField = str_replace('!', '', $sort);
            if (!str_contains($sort, '.')) {
                $cleanedUpSortField = sprintf('%s.%s', $rootAlias, $cleanedUpSortField);
            }
            $queryBuilder->orderBy($cleanedUpSortField, $direction);
        }

        $admin->extendQuery($queryBuilder);

        $search = $request->query->get('search');
        if ($search) {
            $likeValue = $search;
            $field = 'title';
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

        $crudContext = AbstractAdmin::ROUTE_CONTEXT_LIST;
        $templateArguments = [
            'list' => $queryBuilder->getQuery()->getResult(),
            'list_collection' => $listCollection,
            'admin' => $admin,
            'sort' => $sort,
            'search' => $search
        ] + $admin->getTemplateArguments($crudContext);

        return new Response(
            $this->twig->render($admin->getTemplate(AbstractAdmin::ROUTE_CONTEXT_LIST), $templateArguments)
        );
    }

    /**
     * @throws SyntaxError
     * @throws RuntimeError
     * @throws LoaderError
     * @throws Exception
     */
    public function create(Request $request)
    {
        $admin = $this->getAdminFromRequest($request);
        if(!$this->security->isGranted($admin->getRequiredRole())) throw new Exception('Access denied.');

        $form = $this->buildForm($admin);
        $adminListUrl = $this->adminUrlHelper->generateAdminUrl($admin->getEntityClass(), AbstractAdmin::ROUTE_CONTEXT_LIST);

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

        $crudContext = AbstractAdmin::ROUTE_CONTEXT_UPDATE;
        $templateArguments = [
            'form' => $form->createView(),
            'back' => $adminListUrl
        ] + $admin->getTemplateArguments($crudContext);

        return new Response(
            $this->twig->render($admin->getTemplate($crudContext), $templateArguments)
        );
    }

    /**
     * @throws SyntaxError
     * @throws RuntimeError
     * @throws LoaderError
     * @throws EntityNotFoundException
     * @throws Exception
     */
    public function update(Request $request)
    {
        $admin = $this->getAdminFromRequest($request);
        if(!$this->security->isGranted($admin->getRequiredRole())) throw new Exception('Access denied.');

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
        return $this->sort($request, function($position) { return $position + 1; });
    }

    public function moveUp(Request $request)
    {
        return $this->sort($request, function($position) { return $position - 1; });
    }

    private function sort(Request $request, callable $method)
    {
        $admin = $this->getAdminFromRequest($request);
        if(!$this->security->isGranted($admin->getRequiredRole())) throw new Exception('Access denied.');

        $object = $this->getObjectByRequest($admin, $request);

        if (!$object instanceof SortableEntity) {
            throw new Exception('Entity must extend SortableEntity');
        }

        $orderBy = 'ASC';
        $positionMap = array_map(function($instance) {
            return $instance;
        }, $this->entityManager->getRepository($admin->getEntityClass())->findBy([], ['position' => $orderBy]));
        foreach ($positionMap as $position => $instance) {
            if ($object === $instance && isset($positionMap[$method($position)])) {
                $positionMap[$position] = $positionMap[$method($position)];
                $positionMap[$method($position)] = $object;
                break;
            }
        }
        foreach ($positionMap as $position => $instance) {
            $instance->setPosition($position+1);
        }
        $this->entityManager->flush();

        return new RedirectResponse($this->adminUrlHelper->generateAdminUrl($admin->getEntityClass(), AbstractAdmin::ROUTE_CONTEXT_LIST));
    }

    /**
     * @throws Exception
     */
    public function delete(Request $request): RedirectResponse
    {
        $admin = $this->getAdminFromRequest($request);
        if(!$this->security->isGranted($admin->getRequiredRole())) throw new Exception('Access denied.');

        $object = $this->getObjectByRequest($admin, $request);
        $this->entityManager->remove($object);
        $this->entityManager->flush();

        return new RedirectResponse($this->adminUrlHelper->generateAdminUrl($admin->getEntityClass(), AbstractAdmin::ROUTE_CONTEXT_LIST));
    }

    /**
     * @throws Exception
     */
    private function getAdminFromRequest(Request $request): AdminInterface
    {
        return $this->adminPool->getAdminForClass($request->attributes->get('_entity'));
    }

    /**
     * @throws Exception
     */
    private function getObjectByRequest(AdminInterface $admin, Request $request): ?BaseEntity
    {
        $id = $request->attributes->getInt('id');
        $repository = $this->entityManager->getRepository($admin::getEntityClass());
        $object = $repository->find($id);
        if ($object instanceof BaseEntity) {
            return $object;
        }
        throw new Exception(sprintf('Object with id #%d not found!', $id));
    }

    protected function buildForm(AdminInterface $admin, ?BaseEntity $data = null): FormInterface
    {
        $formCollection = new FormCollection();
        $admin->buildForm($formCollection);
        $formUrl = (
            $data ? $this->adminUrlHelper->generateAdminUrl($admin->getEntityClass(), AbstractAdmin::ROUTE_CONTEXT_UPDATE, $data->getId()) : $this->adminUrlHelper->generateAdminUrl($admin->getEntityClass(), AbstractAdmin::ROUTE_CONTEXT_CREATE)
        );

        return $this->formFactory->create(Dynamic::class, $data, [
            'data_class' => $admin::getEntityClass(),
            'form_collection' => $formCollection,
            'action' => $formUrl,
            'method' => 'POST'
        ]);
    }
}
