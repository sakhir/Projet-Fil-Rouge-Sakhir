<?php

namespace App\DataFixtures;

use App\Entity\Profil;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class ProfilFixtures extends Fixture 

{

    public const ADMIN_PROFIL_REFERENCE = 'ADMIN';
    public const APPRENANT_PROFIL_REFERENCE = 'APPRENANT';
    public const FORMATEUR_PROFIL_REFERENCE = 'FORMATEUR';
    public const CM_PROFIL_REFERENCE        = 'CM';


    public function load(ObjectManager $manager)
{
        $Admin = new Profil();
        $Admin->setLibelle(self::ADMIN_PROFIL_REFERENCE);
        $manager->persist($Admin);
        
        $Apprenant = new Profil();
        $Apprenant->setLibelle(self::APPRENANT_PROFIL_REFERENCE);
        $manager->persist($Apprenant);

        $Formateur = new Profil();
        $Formateur->setLibelle(self::FORMATEUR_PROFIL_REFERENCE);
        $manager->persist($Formateur);

        $Cm = new Profil();
        $Cm->setLibelle(self::CM_PROFIL_REFERENCE);
        $manager->persist($Cm);

        $this->addReference(self::ADMIN_PROFIL_REFERENCE, $Admin);
        $this->addReference(self::APPRENANT_PROFIL_REFERENCE, $Apprenant);
       $this->addReference(self::FORMATEUR_PROFIL_REFERENCE, $Formateur);
        $this->addReference(self::CM_PROFIL_REFERENCE, $Cm);
        
         $manager->flush();    
  }
}  