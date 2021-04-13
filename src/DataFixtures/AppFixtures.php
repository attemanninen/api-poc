<?php

namespace App\DataFixtures;

use App\Entity\Customer;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class AppFixtures extends Fixture
{
    /**
     * {@inheritDoc}
     */
    public function load(ObjectManager $manager)
    {
        $customer = new Customer('Example Name');
        $customer->setEmail('example.name@example.com');
        $manager->persist($customer);

        $customer = new Customer('Foo Bar');
        $customer->setEmail('foo.bar@example.com');
        $manager->persist($customer);

        $customer = new Customer('Zoo Bar');
        $customer->setEmail('zoo.bar@example.com');
        $manager->persist($customer);

        $customer = new Customer('Customer without email');
        $manager->persist($customer);

        $manager->flush();
    }
}
