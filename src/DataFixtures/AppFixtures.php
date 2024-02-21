<?php

namespace App\DataFixtures;

use Faker\Factory;
use App\Entity\User;
use App\Entity\Wallet;
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
        for ($i = 0; $i < 5; $i++) {
            $user = new User();
            $user->setFullName($this->faker->name())
                ->setRoles(['ROLE_USER'])
                ->setEmail($this->faker->unique()->email())
                ->setPlainPassword('password');

            // Créez un portefeuille pour chaque utilisateur
            $wallet = new Wallet();
            $wallet->setSolde(500);
            $wallet->setUser($user);
            $manager->persist($user);
            $manager->persist($wallet);
        }
        $admin = new User();
        $admin->setFullName($this->faker->name())
            ->setRoles(['ROLE_ADMIN'])
            ->setEmail($this->faker->unique()->email())
            ->setPlainPassword('adminpassword');
        $manager->persist($admin);

        $manager->flush();
    }
}
