<?php

namespace Undine\AppBundle\DataFixtures\ORM;

use Doctrine\Common\Persistence\ObjectManager;
use Symfony\Bridge\Doctrine\Tests\Fixtures\ContainerAwareFixture;
use Symfony\Component\Security\Core\Encoder\EncoderFactoryInterface;
use Undine\Model\Staff;

class LoadAppData extends ContainerAwareFixture
{
    public function load(ObjectManager $manager)
    {
        /** @var EncoderFactoryInterface $encoderFactory */
        $encoderFactory = $this->container->get('security.encoder_factory');
        $encoder        = $encoderFactory->getEncoder(Staff::class);

        $admin = new Staff('admin@example.com', $encoder->encodePassword('admin', null));
        $admin->setName('Super Administrator');

        $manager->persist($admin);

        $manager->flush();
    }
}
