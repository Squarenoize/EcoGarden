<?php

namespace App\DataFixtures;

use App\Entity\Month;
use App\Entity\Tip;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class AppFixtures extends Fixture
{
    public function load(ObjectManager $manager): void
    {
        $months = [
            ['number' => 1, 'name' => 'Janvier'],
            ['number' => 2, 'name' => 'Février'],
            ['number' => 3, 'name' => 'Mars'],
            ['number' => 4, 'name' => 'Avril'],
            ['number' => 5, 'name' => 'Mai'],
            ['number' => 6, 'name' => 'Juin'],
            ['number' => 7, 'name' => 'Juillet'],
            ['number' => 8, 'name' => 'Août'],
            ['number' => 9, 'name' => 'Septembre'],
            ['number' => 10, 'name' => 'Octobre'],
            ['number' => 11, 'name' => 'Novembre'],
            ['number' => 12, 'name' => 'Décembre'],
        ];

        $monthEntities = [];
        foreach ($months as $monthData) {
            $month = new Month();
            $month->setNumber($monthData['number']);
            $month->setName($monthData['name']);
            $manager->persist($month);
            $monthEntities[] = $month;
        }

        for ($i = 1; $i <= 20; $i++) {
            $tip = new Tip();
            $tip->setTitle("Conseil  $i");
            $tip->setText("Ceci est le texte du conseil $i.");
            $tip->setMonth($monthEntities[array_rand($monthEntities)]);
            
            $manager->persist($tip);
        }

        $manager->flush();
    }
}
