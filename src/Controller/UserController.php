<?php

namespace App\Controller;

use App\Entity\User;
use App\Entity\Apprenant;
use App\Entity\CM;
use App\Entity\Formateur;
use App\Entity\Profil;
use App\Helper\UserHelper;
use App\Repository\ProfilRepository;
use PhpParser\Node\Expr\Cast;
use Doctrine\ORM\Mapping\Entity;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Core\Security;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

class UserController extends AbstractController
{
    
    private $security;
    private $em ;
    private $helper;
    private $repo ;
    private $profilRepo ;

    public function __construct(Security $security ,EntityManagerInterface $em ,UserHelper $helper ,UserRepository $repo ,ProfilRepository $profilRepo)
    {
        $this->security = $security;
        $this->em=$em ;
        $this->helper=$helper ;
        $this->repo=$repo ;
        $this->profilRepo=$profilRepo ;
    }


    // je vais commencer a partir de la à utiliser les services 
    // creeons une fonction addUser qui va creer tout type d utilisateur 
    
    public function addUser(UserHelper $helperUser, SerializerInterface $serializer ,Request $request  ) {
     $userpost =$request->request->all();
      
     $profil= $this->profilRepo->findByProfil($userpost['profil']);
       
     $profil="/api/admin/profils/".$profil[0]->getId();
     $userpost['profil'] =$profil ;
    
     //dd($userpost);
     $profilUser =$serializer->denormalize($userpost['profil'] ,Profil::class);
    // dd($profilUser);
     $user=$serializer->denormalize($userpost,"App\Entity\\".ucfirst(strtolower($profilUser->getLibelle())),true);
     dd($user);
    $helperUser->createUser($request,$user,$userpost,$profilUser);
    return $this->json('create',Response::HTTP_OK);

    }
    public function EditUser($id,Request $request) {
        $data=$request->request->all();
        $user=$this->repo->find($id);
        foreach($data as $key=> $value) {
            $setProperty='set'.ucfirst($key);
            $user->$setProperty($value);

        }
        $image=$this->helper->TRaiterImage($request);
        $user->setAvatar($image);
        $this->em->flush();
         return $this->json('Modification reuissie',Response::HTTP_OK);
    }

     //show all apprenants
     public function showApprenants(UserRepository $repo)
     {
         if($this->isGranted('ROLE_FORMATEUR') || $this->isGranted('ROLE_ADMIN')  || $this->isGranted('ROLE_CM')){
              $apprenants = $repo->findByProfil("APPRENANT");
             return $this->json($apprenants,200,[],['groups'=>'user:read']);
         }else{
             return $this->json("vous n'avez pas accès a cette resource ",403);
         }
     }
 
     //liste des formateurs
     public function showFormateurs(UserRepository $repo)
     {
         if($this->isGranted('ROLE_ADMIN')  || $this->isGranted('ROLE_CM')){
             $formateurs = $repo->findByProfil("FORMATEUR");
            return $this->json($formateurs,200,[],['groups'=>'user:read']);
        }else{
            return $this->json("vous n'avez pas accès a cette resource ",403);
        }
     }

        //get one apprenant by id
        public function findApprenantsById(UserRepository $repo,$id)
        {
            if($this->isGranted('ROLE_ADMIN') || $this->isGranted('ROLE_FORMATEUR') || $this->isGranted('ROLE_CM')){
                $apprenants = $repo->findOneById('APPRENANT', $id);
                if ($apprenants) {
                    return $this->json($apprenants,Response::HTTP_OK,[],['groups'=>"student:read"]);
                }else{
                    return $this->json("user n'est pas un apprenant");
                }
            }else {
                $apprenants = $repo->findOneById('APPRENANT', $id);
                
                $user=$this->security->getUser();
                if (!$apprenants) 
                { return $this->json("vous n'avez pas accès a cette resource ",403); }
                
               if ( $this->isGranted('ROLE_APPRENANT') && $apprenants->getId()==$user->getId() ) {
                    return $this->json($apprenants,Response::HTTP_OK,[],['groups'=>"student:read"]);
                  }else {
                    return $this->json("vous n'avez pas accès a cette resource ",403); 
                }
        
            } 
        }
    
    
  //get one formateur by id
  public function findFormateursById(UserRepository $repo,$id)
  {
      if($this->isGranted('ROLE_CM') || $this->isGranted('ROLE_ADMIN')){
          $formateurs = $repo->findOneById('FORMATEUR', $id);
          if ($formateurs) {
              return $this->json($formateurs,Response::HTTP_OK,[],['groups'=>'user:read']);
          }else{
              return $this->json("user n'est pas un formateur");
          }
      }

      else {
          $formateurs = $repo->findOneById('FORMATEUR', $id);
          $user=$this->security->getUser();
          if ($formateurs==null) 
          { return $this->json("vous n'avez pas accès a cette resource ",403); }
          
          if ( $this->isGranted('ROLE_FORMATEUR') && $user->getId()==$formateurs->getId() ) {
              return $this->json($formateurs,Response::HTTP_OK,[],['groups'=>'user:read']);
          }
          else {
              return $this->json("vous n'avez pas accès a cette resource ",403); 
          }
      }      
  }

 //edit apprenant by id
 public function editApprenant(UserRepository $repo,$id,Request $request, EntityManagerInterface $em, ValidatorInterface $validator)
 {
     $apprenantObject = $repo->findOneById('APPRENANT', $id);
     if($apprenantObject==null ) {
         return $this->json("vous n'avez pas accès a cette resource ",403); 
     }
     $user=$this->security->getUser();
     if($this->isGranted('ROLE_FORMATEUR') || $this->isGranted('ROLE_ADMIN') || ($this->isGranted('ROLE_APPRENANT') && $user->getId()==$apprenantObject->getId()) ){
         $jsonApprenant  = json_decode($request->getContent());
        
         $apprenantObject->setNom($jsonApprenant->nom);
         $apprenantObject->setPrenom($jsonApprenant->prenom);
         $apprenantObject->setEmail($jsonApprenant->email);

         if($apprenantObject){
             $erreurs = $validator->validate($apprenantObject);
             if (count($erreurs)>0) {
                 return $this->json('invalide',Response::HTTP_BAD_REQUEST);
             }
             $em->flush();
             return $this->json('success',Response::HTTP_OK);
         }else{
             return $this->json("user n'est pas un apprenant");
         }
     }else{
         return $this->json("vous n'avez pas accès a cette resource ",403);
     }
 }

 
     //editer un formateur
     public function editFormateur(UserRepository $repo,$id,Request $request, EntityManagerInterface $em, ValidatorInterface $validator)
     {
         $formateurObject = $repo->findOneById('FORMATEUR', $id);
         if($formateurObject==null ) {
             return $this->json("vous n'avez pas accès a cette resource ",403); 
         }
         $user=$this->security->getUser();
         if( ($this->isGranted('ROLE_FORMATEUR') && $user->getId()==$formateurObject->getId())  || $this->isGranted('ROLE_ADMIN') ){
 
             $jsonFormateur  = json_decode($request->getContent());
         
             $formateurObject->setNom($jsonFormateur->nom);
             $formateurObject->setPrenom($jsonFormateur->prenom);
             $formateurObject->setEmail($jsonFormateur->email);
 
             if($formateurObject){
                 $erreurs = $validator->validate($formateurObject);
                 if (count($erreurs)>0) {
                     return $this->json('invalide',Response::HTTP_BAD_REQUEST);
                 }
                 $em->flush();
                 return $this->json('success',Response::HTTP_OK);
             }else{
                 return $this->json("user n'est pas un formateur");
             }
         }else{
 
             return $this->json("vous n'avez pas accès a cette resource ",403);
         }
     }
  
    //create user
/*
    public function addUser(Request $request,SerializerInterface $serializer,ValidatorInterface $validator,EntityManagerInterface $em, UserPasswordEncoderInterface $encoder)
    {

        $user = $request->request->all();
        $avatar = $request->files->get("avatar");
       //testons quel type de user on va inseré
        if ($user['type'] == "APPRENANT") {
            $user = $serializer->denormalize($user,Apprenant::class,true);
        }elseif ($user['type'] == "FORMATEUR") {
            $user = $serializer->denormalize($user,Formateur::class,true);
           
        }elseif ($user['type'] == "CM") {
            
            $user = $serializer->denormalize($user,CM::class,true);
           
        }else{
            $user = $serializer->denormalize($user,"App\Entity\User",true);
        }
        //dd($user);
        $avatar = fopen($avatar->getRealPath(),"rb");
        $user->setAvatar($avatar);
        $errors = $validator->validate($user);
        if (count($errors)){
            $errors = $this->serializer->serialize($errors,"json");
            return new JsonResponse($errors,Response::HTTP_BAD_REQUEST,[],true);
        }
       
        $password = $user->getPassword();
        $user->setPassword($encoder->encodePassword($user,$password));
        $user->setIsconnect("0");
        $user->setIsdeleted("0");
    
      
       
        $em->persist($user);
         
        $em->flush();
        fclose($avatar);
        
        return $this->json("success",201);
    }      */
       
    //archiver user 
    public function deleteUser(UserRepository $repo,$id,EntityManagerInterface $em)
    {
        $user = $repo->find($id);
        if ($this->isGranted('ROLE_ADMIN') && $user != null) {
            $user->setIsdeleted(1);
            $em->flush();
            return $this->json('deleted',Response::HTTP_OK); 
        }
        return $this->json("access denied or not user !!!");
    }
 
    
}
