<?php

namespace Undine\AppBundle\Menu;

use Knp\Menu\FactoryInterface;

class Builder
{
    public function adminMenu(FactoryInterface $factory, array $options)
    {
        $menu = $factory->createItem('root');
        $menu->setChildrenAttribute('class', 'nav navbar-nav');
        $menu->addChild('Dashboard', ['route' => 'admin-home']);
        $menu->addChild('Staff', ['route' => 'admin-staff_list']);
        $menu->addChild('Organizations', ['route' => 'admin-organization_list']);
        $menu->addChild('Users', ['route' => 'admin-user_list']);

        return $menu;
    }
}
