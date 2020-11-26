<?php


namespace App\DataPersister;


use ApiPlatform\Core\DataPersister\ContextAwareDataPersisterInterface;
use App\Entity\Profil;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\Persistence\ObjectManager;

final class ProfilDataPersister implements ContextAwareDataPersisterInterface
{

    private $manager;

    public function __construct(EntityManagerInterface $manager)
    {
        $this->manager = $manager;
    }

    public function supports($data, array $context = []): bool
    {
        return $data instanceof Profil;
    }

    public function persist($data, array $context = [])
    {
      // call your persistence layer to save $data
       $data->setLibelle($data->getLibelle());
       $data->setArchivage(0);
       $this->manager->persist($data);
       $this->manager->flush();
 
      // dd($data->getLibelle(),$data->getArchivage());

      return $data;

    }

    public function remove($data, array $context = [])
    {
        
      // call your persistence layer to delete $data
      $data->setArchivage(1);
      $this->manager->flush();

    }
}