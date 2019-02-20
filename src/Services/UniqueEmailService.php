<?php 

namespace App\Services;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;
use App\Entity\User; 
use App\Form\UserType;
use App\Form\ProfileFormType;
use App\Form\PasswordFormType;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

class UniqueEmailService extends AbstractController {

    public function isUnique($email = NULL){        
        return $this->getDoctrine()->getRepository(User::class)->findOneBy(['email' => $email]);                
    }

}