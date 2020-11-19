<?php

namespace App\DataFixtures;

use Faker\Factory;
use App\Entity\User;
use App\Entity\Apprenant;
use App\Entity\CM;
use App\Entity\Formateur;
use Doctrine\Persistence\ObjectManager;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

class UserFixtures extends Fixture
{

    private $encode;
    
    public function __construct(UserPasswordEncoderInterface $encode)
    {
        $this->encode = $encode;
      
    } 
  

    public function load(ObjectManager $manager)
    {

        $faker = Factory::create('fr_FR');

        for ($i=1; $i <=3 ; $i++) {           
            $admin = new User;           
            $admin ->setEmail($faker->email);
            $admin ->setPrenom($faker->firstName);
            $admin ->setNom($faker->lastName);
            $admin ->setProfil($this->getReference(ProfilFixtures::ADMIN_PROFIL_REFERENCE));
            $admin->setIsconnect("0");
            $admin->setIsdeleted("0");
            $password = $this->encode->encodePassword ($admin, 'passer');           
            $admin ->setPassword($password);  

            $manager->persist($admin);  

        }
        for ($i=1; $i <=3 ; $i++) {           
            $apprenant = new Apprenant;           
            $apprenant ->setPrenom($faker->firstName);
            $apprenant ->setNom($faker->lastName);
            $apprenant ->setEmail($faker->email);
            $apprenant ->setProfil($this->getReference(ProfilFixtures::APPRENANT_PROFIL_REFERENCE));
            $apprenant->setIsconnect("0");
            $apprenant->setIsdeleted("0");
            $apprenant->setTelephone($faker->phoneNumber);
            $apprenant->setGenre("masculin");
            $apprenant->setAdresse($faker->city);
            $apprenant->setStatut("actif");
            $password = $this->encode->encodePassword ($apprenant, 'passer');           
            $apprenant->setPassword($password);  

            $manager->persist($apprenant);  

        }
        
        for ($i=1; $i <=3 ; $i++) {           
            $formateur = new Formateur;           
            $formateur ->setPrenom($faker->firstName);
            $formateur ->setNom($faker->lastName);
            $formateur ->setEmail($faker->email);
            $formateur ->setProfil($this->getReference(ProfilFixtures::FORMATEUR_PROFIL_REFERENCE));
            $formateur->setIsconnect("0");
            $formateur->setIsdeleted("0");
            $password = $this->encode->encodePassword ($formateur, 'passer');           
            $formateur ->setPassword($password);  
            $manager->persist($formateur);  

        }
        for ($i=1; $i <=3 ; $i++) {           
            $cm = new CM;           
            $cm ->setPrenom($faker->firstName);
            $cm ->setNom($faker->lastName);
            $cm ->setEmail($faker->email);
            $cm ->setProfil($this->getReference(ProfilFixtures::CM_PROFIL_REFERENCE));
            $cm->setIsconnect("0");
            $cm->setIsdeleted("0");
            $password = $this->encode->encodePassword ($cm, 'passer');           
            $cm->setPassword($password);  
            $manager->persist($cm);  

        }
        $manager->flush();   
    }

    
}