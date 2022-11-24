<?php

namespace App\DataFixtures;

use App\Entity\Hobby;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class HobbyFixture extends Fixture
{
    public function load(ObjectManager $manager): void
    {
        $data = [
            "football",
            "basketball",
            "peinture",
            "jeux-vidéo",
            "chant",
            "cuisine",
            "yoga",
            "vélo",
            "lecture",
            "tennis",
            "dessin",
            "photographie",
            "poterie",
        ];

        for ($i=0; $i<count($data);$i++) {
            $hobby = new Hobby();
            $hobby->setDesignation($data[$i]);
            $manager->persist($hobby);
        }
        $manager->flush();
    }
}
