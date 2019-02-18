<?php

namespace App\Doctrine;

use Doctrine\Common\EventSubscriber;
Use App\Entity\User;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use Doctrine\ORM\Event\LifecycleEventArgs;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class PasswordListener extends AbstractController implements EventSubscriber{

    private $encoder;

    public function __construct(UserPasswordEncoderInterface $passwordEncoder){
        $this->encoder = $passwordEncoder;
    }

    public function getSubscribedEvents(){
        return ['prePersist', 'preUpdate'];
    }

    public function prePersist(LifecycleEventArgs $args){
        $entity = $args->getEntity();
        if (!$entity instanceof User) {
            return;
        }
        $this->hashPassword($entity);
    }

    public function preUpdate(LifecycleEventArgs $args){        
        $entity = $args->getEntity();
        if (!$entity instanceof User) {
            return;
        }        
        $this->hashPassword($entity);
        $em = $args->getEntityManager();
        $meta = $em->getClassMetadata(get_class($entity));
        $em->getUnitOfWork()->recomputeSingleEntityChangeSet($meta, $entity);
    }

    private function hashPassword(User $entity){                
        if(!$entity->getPassword() || empty($entity->getPassword()) || !$entity->getPassword() === strtolower('x')){
            $user = $this->getDoctrine()->getRepository(User::class)->find($entity->getId()); 
            $entity->setPassword($user->getPassword());
        } else {
            $hashedPassword = $this->encoder->encodePassword($entity, $entity->getPassword());
            $entity->setPassword($hashedPassword);
        }
    }

}

