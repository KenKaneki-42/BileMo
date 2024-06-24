<?php

namespace App\DataFixtures;

use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Faker\Factory;
use App\Entity\Customer;

class CustomerFixtures extends Fixture
{
    public function load(ObjectManager $manager)
    {
        $faker = Factory::create();

        for ($i = 0; $i < 30; $i++) {
            $customer = new Customer();
            $customer->setFirstName($faker->firstName());
            $customer->setLastName($faker->lastName());
            $customer->setEmail($faker->email());
            $createdAt = $faker->dateTimeBetween('-1 year', 'now');
            $createdAtImmutable = \DateTimeImmutable::createFromMutable($createdAt);
            $customer->setCreatedAt($createdAtImmutable);
            $manager->persist($customer);
        }

        $manager->flush();
    }
}
