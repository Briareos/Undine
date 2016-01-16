<?php

namespace Undine\AppBundle\Menu;

use Doctrine\ORM\EntityManagerInterface;
use Knp\Menu\FactoryInterface;
use Knp\Menu\ItemInterface;
use Symfony\Component\DependencyInjection\ContainerAwareInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Undine\Model\Staff;
use Undine\Model\User;

class Breadcrumbs implements ContainerAwareInterface
{
    /**
     * @var ContainerInterface
     */
    private $container;

    public function breadcrumbsMenu(FactoryInterface $factory, array $options)
    {
        $request = $this->container->get('request_stack')->getMasterRequest();
        $route = $request->get('_route');

        $menu = $factory->createItem('root');
        $menu->setChildrenAttribute('class', 'ui breadcrumb');
        $menu->addChild('Dashboard', ['route' => 'admin-home']);

        if (strncmp($route, 'admin-staff', strlen('admin-staff')) === 0) {
            $this->staffMenu($menu);
        }

        if (strncmp($route, 'admin-user', strlen('admin-user')) === 0) {
            $this->userMenu($menu);
        }

        return $menu;
    }

    private function staffMenu(ItemInterface $menu)
    {
        $route = $this->getRoute();
        $staffId = $this->getRequestAttribute('id');

        if ($staffId !== null) {
            /** @var Staff $staff */
            $staff = $this->getEntityManager()->find(Staff::class, $staffId);
        }

        $menu->addChild('Staff', ['route' => 'admin-staff_list']);

        if (isset($staff) && in_array($route, ['admin-staff_view', 'admin-staff_edit', 'admin-staff_delete'])) {
            $menu->addChild($staff->getEmail(),
                [
                    'route' => 'admin-staff_view',
                    'routeParameters' => ['id' => $staffId],
                ]);
        }

        $routeLabelMap = [
            'admin-staff_create' => 'Create',
            'admin-staff_edit' => 'Edit',
            'admin-staff_delete' => 'Delete',
        ];

        if (isset($routeLabelMap[$route])) {
            $menu->addChild($routeLabelMap[$route]);
        }
    }

    private function userMenu(ItemInterface $menu)
    {
        $route = $this->getRoute();
        $userId = $this->getRequestAttribute('id');

        if ($userId !== null) {
            /** @var User $user */
            $user = $this->getEntityManager()->find(User::class, $userId);
        }

        $menu->addChild('User', ['route' => 'admin-user_list']);

        if (isset($user) && in_array($route, ['admin-user_view', 'admin-user_edit', 'admin-user_delete'])) {
            $menu->addChild($user->getEmail(),
                [
                    'route' => 'admin-user_view',
                    'routeParameters' => ['id' => $userId],
                ]);
        }

        $routeLabelMap = [
            'admin-user_create' => 'Create',
            'admin-user_edit' => 'Edit',
            'admin-user_delete' => 'Delete',
        ];

        if (isset($routeLabelMap[$route])) {
            $menu->addChild($routeLabelMap[$route]);
        }
    }

    protected function getRoute()
    {
        return $this->getRequest()->get('_route');
    }

    protected function getRequestAttribute($attribute, $default = null)
    {
        return $this->getRequest()->attributes->get($attribute, $default);
    }

    protected function getRequest()
    {
        return $this->container->get('request_stack')->getMasterRequest();
    }

    /**
     * @return EntityManagerInterface
     */
    protected function getEntityManager()
    {
        return $this->container->get('doctrine.orm.default_entity_manager');
    }

    public function setContainer(ContainerInterface $container = null)
    {
        $this->container = $container;
    }
}
