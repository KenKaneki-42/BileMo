<?php

namespace App\DataFixtures;

use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Faker\Factory;
use App\Entity\User;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class UserFixtures extends Fixture
{
    private $passwordHasher;

    public function __construct(UserPasswordHasherInterface $passwordHasher)
    {
        $this->passwordHasher = $passwordHasher;
    }

    public function load(ObjectManager $manager): void
    {
        $faker = Factory::create();

        for ($i = 1; $i < 11; $i++) {
            $user = new User();
            $user->setName($faker->company());
            $user->setEmail($faker->email);
            $user->setPassword(
                $this->passwordHasher->hashPassword($user, 'password')
            );
            $user->setApiKey($faker->uuid);
            $user->setRoles(['ROLE_USER']);
            $createdAt = $faker->dateTimeBetween('-1 year', '-3 months');
            $createdAtImmutable = \DateTimeImmutable::createFromMutable($createdAt);
            $user->setCreatedAt($createdAtImmutable);
            $user->setUpdatedAt($faker->dateTimeBetween('-3 monts', 'now'));
            $manager->persist($user);
            $this->addReference('user-'.$i, $user);
        }

        $manager->flush();
    }
}
