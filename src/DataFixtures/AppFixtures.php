<?php

namespace App\DataFixtures;

use Faker\Factory;
use App\Entity\User;
use App\Entity\Profil;
use App\Repository\ProfilRepository;
use Doctrine\Persistence\ObjectManager;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

class AppFixtures extends Fixture

{

    private $encode;
    private $profils;

    public function __construct(UserPasswordEncoderInterface $encode,ProfilRepository $ProfilSortie)
    {
        $this->encode = $encode;
        $this->profils = $ProfilSortie;
        
    } 
    public function load(ObjectManager $manager)
    {
        /*
        // $product = new Product();
        // $manager->persist($product);
        $faker = Factory::create('fr_FR');
        $profils=["ADMIN","FORMATEUR","APPRENANT","CM"];
       foreach ($profils as $key => $libelle) {
                $profil =new Profil();  
                $profil ->setLibelle($libelle);
                 $manager ->persist($profil);        
                 $manager ->flush();         
                for ($i=1; $i <=3 ; $i++) {           
                    $user = new User();           
                    $user ->setProfil($profil);
                    $user ->setEmail($faker->email);
                    $user ->setPrenom($faker->firstName);
                    $user->setIsdeleted("0");          
                    $user->setIsconnect("0");  
                     $user ->setNom($faker->name());           
                     //Génération des Users          
                      $password = $this->encode->encodePassword ($user, 'passer');           
                      $user ->setPassword($password);                      
                      $manager ->persist($user);                  
                     }

        $manager->flush();
       } */
}
}