<?php

namespace Undine\AppBundle\Menu;

use Knp\Menu\FactoryInterface;
use Symfony\Component\DependencyInjection\ContainerAwareInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

class Builder implements ContainerAwareInterface
{
    /** @var ContainerInterface */
    private $container;

    public function adminMenu(FactoryInterface $factory, array $options)
    {
        $menu = $factory->createItem('root');
        $menu->setChildrenAttribute('class', 'ui container');
        $menu->addChild('Dashboard', ['route' => 'admin-home']);
        $menu->addChild('Staff', ['route' => 'admin-staff_list']);
        $menu->addChild('Users', ['route' => 'admin-user_list']);

        $rightMenu = $menu->addChild('RightMenu', [
            'childrenAttributes' => ['class' => 'right menu'],
            'extras'             => ['type' => 'right-menu'],
        ]);

        $rightMenu->addChild('Logout', [
            'uri' => $this->container->get('security.logout_url_generator')->getLogoutUrl('admin'),
        ]);

        return $menu;
    }

    /**
     * @param ContainerInterface $container
     */
    public function setContainer(ContainerInterface $container = null)
    {
        $this->container = $container;
    }

}
