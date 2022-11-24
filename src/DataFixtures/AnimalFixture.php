<?php

namespace App\DataFixtures;

use App\Entity\Animal;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Faker\Factory;

class AnimalFixture extends Fixture
{
    public function load(ObjectManager $manager): void
    {
        $espece = [
          "chat",
          "chien",
          "lapin",
          "furet",
          "poisson",
          "oiseau",
          "cochon-d'inde",
          "rat",
          "cheval",
          "poule",
          "cochon",
          "ane",
          "loutre",
          "singe",
        ];


        $faker = Factory::create('fr_FR');
        for ($i=0; $i<100; $i++) {
            $y = rand(0, 13);
            $animal = new Animal();
            $animal->setName($faker->firstName);
            $animal->setEspece($espece[$y]);
            $animal->setColor($faker->colorName);
            $animal->setAge($faker->numberBetween(0,20));

            $manager->persist($animal);
        }
        $manager->flush();
    }
}
