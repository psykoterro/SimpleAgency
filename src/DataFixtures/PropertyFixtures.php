<?php

namespace App\DataFixtures;

use App\Entity\Property;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\OrderedFixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;
use Faker\Factory;
use Symfony\Component\HttpFoundation\File\UploadedFile;

class PropertyFixtures extends Fixture implements OrderedFixtureInterface
{
    public function load(ObjectManager $manager)
    {
        $faker = Factory::create('fr_FR');

        for ($i = 0; $i < 100; $i++) {
            $property = new Property();
            $files = [];

            $property->setAddress($faker->address)
                ->setLat($faker->latitude)
                ->setLng($faker->longitude)
                ->setBedrooms($faker->numberBetween(1, 9))
                ->setCity($faker->city)
                ->setDescription($faker->sentences(3, true))
                ->setFloor($faker->numberBetween(0, 15))
                ->setHeat($faker->numberBetween(0, count(Property::HEAT) - 1))
                ->setPostalCode($faker->postcode)
                ->setPrice($faker->numberBetween(35000, 1000000))
                ->setRooms($faker->numberBetween(1, 10))
                ->setSurface($faker->numberBetween(20, 350))
                ->setTitle($faker->unique()->words(3, true));
            $randomNumber = $faker->numberBetween(1, 3);
            for ($j = 0; $j < $randomNumber; $j++) {
                $randomOption = $faker->numberBetween(1, 3);
                $property->addOption($this->getReference('option'.$randomOption));
            }
            for ($j = 0; $j < $randomNumber; $j++) {
                $getFileFromFaker = fopen($faker->unique()->imageUrl(640, 480, 'city'), 'r');
                $temp = tempnam(sys_get_temp_dir(), 'TMP_');
                file_put_contents($temp, stream_get_contents($getFileFromFaker));
                $files[] = new UploadedFile(
                    $temp,
                    'Image'.$i,
                    null,
                    null,
                    null,
                    true
                );
            }
            $property->setPictureFiles($files)
            ->setUpdatedAt(new \DateTime('now'));

            $manager->persist($property);
        }


        $manager->flush();
    }

    public function getOrder()
    {
        return 3;
    }
}
