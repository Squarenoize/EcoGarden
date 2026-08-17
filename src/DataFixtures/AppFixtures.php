<?php

namespace App\DataFixtures;

use App\Entity\Month;
use App\Entity\Tip;
use App\Entity\User;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class AppFixtures extends Fixture
{
    private UserPasswordHasherInterface $passwordHasher;

    public function __construct(UserPasswordHasherInterface $passwordHasher)
    {
        $this->passwordHasher = $passwordHasher;
    }

    public function load(ObjectManager $manager): void
    {
        $user = new User();
        $user->setEmail('user@ecogarden.com');
        $user->setRoles(['ROLE_USER']);
        $user->setPassword($this->passwordHasher->hashPassword($user, 'password'));
        $user->setZipCode('75001');
        $manager->persist($user);

        $admin = new User();
        $admin->setEmail('admin@ecogarden.com');
        $admin->setRoles(['ROLE_ADMIN']);
        $admin->setPassword($this->passwordHasher->hashPassword($admin, 'password'));
        $admin->setZipCode('46230');
        $manager->persist($admin);

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
            
            // Associer 1 à 3 mois consécutifs à chaque conseil
            $numberOfMonths = rand(1, 3);
            $startIndex = rand(0, 11); // 0 à 11 pour les 12 mois
            
            for ($j = 0; $j < $numberOfMonths; $j++) {
                // Utiliser modulo pour boucler (nov, déc, jan par exemple)
                $monthIndex = ($startIndex + $j) % 12;
                $tip->addMonth($monthEntities[$monthIndex]);
            }
            
            $manager->persist($tip);
        }

        $manager->flush();
    }
}
