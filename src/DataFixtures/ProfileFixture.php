<?php

namespace App\DataFixtures;

use App\Entity\Profile;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class ProfileFixture extends Fixture
{
    public function load(ObjectManager $manager): void
    {
        $profile = new Profile();
        $profile->setRs('Facebook');
        $profile->setUrl('https://www.facebook.com');

        $profile1 = new Profile();
        $profile1->setRs('Twitter');
        $profile1->setUrl('https://www.twitter.com');

        $profile2 = new Profile();
        $profile2->setRs('LinkedIn');
        $profile2->setUrl('https://www.linkedIn.com');

        $profile3 = new Profile();
        $profile3->setRs('GitHub');
        $profile3->setUrl('https://www.github.com');

        $manager->persist($profile);
        $manager->persist($profile1);
        $manager->persist($profile2);
        $manager->persist($profile3);
        $manager->flush();
    }
}
