<?php

declare(strict_types=1);

namespace EDB\AdminBundle\MenuBuilder;

use EDB\AdminBundle\Admin\AbstractAdmin;
use EDB\AdminBundle\Admin\Pool;
use Exception;
use ReflectionException;
use Symfony\Component\Routing\RouterInterface;
use Symfony\Component\Security\Core\Security;

class MenuBuilder
{
    private Pool $pool;
    private RouterInterface $router;
    private Security $security;
    private array $customMenuItems = [];

    public function __construct(Pool $pool, RouterInterface $router, Security $security, $customMenuItems = [])
    {
        $this->pool = $pool;
        $this->router = $router;
        $this->security = $security;

        foreach ($customMenuItems as $customMenuItem) {
            $this->customMenuItems[] = $customMenuItem;
        }
    }

    /**
     * @throws ReflectionException
     */
    public function getElements(): array
    {
        static $elements = [];
        static $all = [];
        foreach ($this->pool->getAdmins() as $admin) {
            if(!$this->security->isGranted($admin->getRequiredRole())) continue;
            $path = $admin->getPath(AbstractAdmin::ROUTE_CONTEXT_LIST);

            try {
                $this->router->generate($path);
                $all[] = new MenuItem($admin->getAdminMenuTitle(), [
                    MenuItem::OPTION_PATH => $path,
                    MenuItem::OPTION_ORDER_NAME => $admin->getAdminMenuOrderName(),
                    MenuItem::OPTION_GROUP => $admin->getAdminMenuGroup(),
                    MenuItem::OPTION_ICON => $admin->getAdminMenuIcon()
                ]);
            } catch (Exception $exception) {
                // Could not generate route.
            }
        }

        foreach ($this->customMenuItems as $customMenuItem) {
            /** @var MenuItemInterface $customMenuItem */
            if (!$this->security->isGranted($customMenuItem->getRequiredRole())) continue;
            $all[] = new MenuItem(
                $customMenuItem->getTitle(), [
                    MenuItem::OPTION_PATH => $customMenuItem->getRouteName(),
                    MenuItem::OPTION_ORDER_NAME => $customMenuItem->getMenuOrderName(),
                    MenuItem::OPTION_GROUP => $customMenuItem->getMenuGroup(),
                    MenuItem::OPTION_ICON => $customMenuItem->getMenuIcon()
                ]
            );
        }

        $groups = [];
        foreach ($all as $element) {
            $groupName = $element->getOption(MenuItem::OPTION_GROUP);
            $orderName = $element->getOption(MenuItem::OPTION_ORDER_NAME) ?? $element->getName();
            if ($groupName) {
                if (!key_exists($orderName, $groups[$groupName])) $groups[$groupName][$orderName] = [];
                $groups[$groupName][$orderName][] = $element;
            } else {
                if (!key_exists($orderName, $elements)) $elements[$orderName] = [];
                $elements[$orderName][] = $element;
            }
        }

        foreach ($groups as $groupName => $items) {
            ksort($items);
            $elements[$groupName] = new MenuGroup($groupName, [MenuGroup::OPTION_ITEMS => $items]);
        }
        ksort($elements);

        return array_merge(...array_values($elements));
    }
}
