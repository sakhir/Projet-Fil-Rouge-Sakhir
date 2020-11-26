<?php

namespace App\Helper;

use App\Entity\Profil;
use Doctrine\Persistence\ObjectManager;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

class UserHelper   

{    private $manager;
     private $encode;  
     private $request;

    public function __construct(EntityManagerInterface $manager , UserPasswordEncoderInterface $encode )
    {
        $this->manager = $manager;
        $this->encode = $encode;
    }

    public function createUser($request,$user,$post,$profil) {
     
        
        $user->setPrenom($post['prenom']);
        $user->setNom($post['nom']);
        $user->setEmail($post['email']);
        $user->setProfil($profil);
        $user->setPassword($this->encode->encodePassword($user,$post['password']));
        $user->setIsconnect("0");
        $user->setIsdeleted("0");
        $image=$this->TraiterImage($request);
        $user->setAvatar($image);
         if($profil->getLibelle()=='APPRENANT') {
             $user->setAdresse($post['addresse']);
             $user->setTelephone($post['telephone']);
             $user->setStatut($post['statut']);
            

         }
        // dd($user);
         $this->manager->persist($user);
         $this->manager->flush();
         return $user;

    }
   
    public function TRaiterImage($request) 
    {
        $avatar = $request->files->get("avatar");
        $image = fopen($avatar->getRealPath(),"rb");
        return $image;

    } 

}