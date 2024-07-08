<?php

declare(strict_types=1);

namespace App\DataFixtures;

use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Faker\Factory;
use App\Entity\Phone;

class PhoneFixtures extends Fixture
{
    public function load(ObjectManager $manager): void
    {
        $faker = Factory::create();

        for ($i = 0; $i < 30; $i++) {
            $phone = new Phone();
            $phone->setBrand($faker->company());
            $phone->setModel($faker->word());
            $phone->setPrice($faker->randomFloat(2, 50, 1000));
            $phone->setDescription($faker->text());
            $createdAt = $faker->dateTimeBetween('-1 year', '-3 months');
            $createdAtImmutable = \DateTimeImmutable::createFromMutable($createdAt);
            $phone->setCreatedAt($createdAtImmutable);
            $phone->setUpdatedAt($faker->dateTimeBetween('-3 months', 'now'));
            $manager->persist($phone);
        }

        $manager->flush();
    }
}
