<?php

namespace App\DataFixtures;

use Faker\Factory;
use App\Entity\User;
use Faker\Generator;
use Doctrine\Persistence\ObjectManager;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class AppFixtures extends Fixture
{
    /**
     * @var Generator
     */
    private Generator $faker;

    public function __construct()
    {
        $this->faker = Factory::create('fr_FR');
    }

    public function load(ObjectManager $manager): void
    {
        //Users
        for ($i = 0; $i < 10; $i++) {
            $user = new User();
            $user->setFullName($this->faker->name())
                ->setRoles(['ROLE_USER'])
                ->setEmail($this->faker->email())
                ->setPlainPassword('password');
            $manager->persist($user);
        }
        for ($i = 0; $i < 1; $i++) {
            $user = new User();
            $user->setFullName($this->faker->name())
                ->setRoles(['ROLE_ADMIN'])
                ->setEmail($this->faker->email())
                ->setPlainPassword('adminpassword');
            $manager->persist($user);
        }

        $manager->flush();
    }
}
