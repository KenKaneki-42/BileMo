<?php

namespace App\DataFixtures;

use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Persistence\ObjectManager;
use Faker\Factory;
use App\Entity\Customer;

class CustomerFixtures extends Fixture implements DependentFixtureInterface
{
    public function load(ObjectManager $manager)
    {
        $faker = Factory::create();

        for ($i = 0; $i < 30; $i++) {
            $customer = new Customer();
            $customer->setFirstName($faker->firstName());
            $customer->setLastName($faker->lastName());
            $customer->setEmail($faker->email());
            $createdAt = $faker->dateTimeBetween('-1 year', '-3 months');
            $createdAtImmutable = \DateTimeImmutable::createFromMutable($createdAt);
            $customer->setCreatedAt($createdAtImmutable);
            $customer->setUpdatedAt($faker->dateTimeBetween('-3 months', 'now'));

            $userReference = 'user-' . rand(1, 10);
            $user = $this->getReference($userReference);
            $customer->setUser($user);

            $manager->persist($customer);
        }

        $manager->flush();
    }

    public function getDependencies()
    {
        return [
            UserFixtures::class,
        ];
    }
}
