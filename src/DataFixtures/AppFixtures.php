<?php

namespace App\DataFixtures;

use App\Entity\Message;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Faker\Factory;

class AppFixtures extends Fixture
{
    public function load(ObjectManager $manager): void
    {
        $faker = Factory::create();

        for ($i = 0; $i < 5; $i++) {
            $message = Message::compose($faker->sentence(), Message::STATUS_SENT);
            $manager->persist($message);
        }

        for ($i = 0; $i < 5; $i++) {
            $message = Message::compose($faker->sentence(), Message::STATUS_READ);
            $manager->persist($message);
        }

        $manager->flush();
    }
}
