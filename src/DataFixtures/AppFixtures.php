<?php

namespace App\DataFixtures;

use Faker\Factory;
use App\Entity\User;
use App\Entity\Wallet;
use App\Entity\CryptoCurrency;
use Faker\Generator;
use Doctrine\Persistence\ObjectManager;
use Doctrine\Bundle\FixturesBundle\Fixture;

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
        // Création de 5 utilisateurs
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
        // Création d'un admin
        $admin = new User();
        $admin->setFullName($this->faker->name())
            ->setRoles(['ROLE_ADMIN'])
            ->setEmail($this->faker->unique()->email())
            ->setPlainPassword('adminpassword');
        $manager->persist($admin);

        // Création des cryptomonnaies
        $cryptoNames = ['Bitcoin', 'Ethereum', 'Ripple', 'Bitcoin Cash', 'Cardano', 'Litecoin', 'NEM', 'Stellar', 'IOTA', 'Dash'];

        foreach ($cryptoNames as $cryptoName) {
            $cryptoCurrency = new CryptoCurrency();
            $cryptoCurrency->setName($cryptoName);
            $firstCotation = $this->getFirstCotation($cryptoName);
            $dailyPrices = [];
            $currentDate = new \DateTime();
            // On va faire des fixtures pour les 30 derniers jours
            for ($i = 0; $i < 31; $i++) {
                $date = $currentDate->format('Y-m-d');
                $cotationFor = $this->getCotationFor($cryptoName);
                $dailyPrices[$date] = $firstCotation + $cotationFor;
                if ($i == 0) {
                    $cryptoCurrency->setCurrentPrice($firstCotation + $cotationFor); // Affecter la première cotation à setCurrentPrice
                }
                $currentDate->modify('-1 day');
            }
            ksort($dailyPrices);
            $cryptoCurrency->setDailyPrices($dailyPrices);
            $manager->persist($cryptoCurrency);
        }
        // Sauvegarde des données
        $manager->flush();
    }

    // Fonctions predefinie
    private function getFirstCotation($cryptoname)
    {
        return ord(substr($cryptoname, 0, 1)) + rand(0, 10);
    }
    function getCotationFor($cryptoname)
    {
        return ((rand(0, 99) > 40) ? 1 : -1) * ((rand(0, 99) > 49) ? ord(substr($cryptoname, 0, 1)) : ord(substr($cryptoname, -1))) * (rand(1, 10) * .01);
    }
}
