<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;
use App\Entity\User; 
use App\Form\UserType;
use App\Form\ProfileFormType;
use App\Form\PasswordFormType;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

class DefaultController extends AbstractController
{
    /**
     * @Route("/home/welcome", name="login_welcome")
     */
    public function index()
    {
        return $this->render('default/index.html.twig', [
            'controller_name' => 'Welcome',
            'profile_url' => $this->generateUrl('user_profile'),
            'profile_password_url' => $this->generateUrl('update_profile_password')
        ]);
    }

    /**
    * @Route("/home/profile", name="user_profile")
    */

    Public function profile(Request $request){

        $user = $this->getDoctrine()->getRepository(User::class)->find($this->getUser()->getId());        

        $form = $this->createForm(ProfileFormType::class, $user);
        
        
        if ($request->isMethod('POST')) {
            $form->handleRequest($request);
            
            if($form->isSubmitted() && $form->isValid()){                     
                $em = $this->getDoctrine()->getManager();            
                $em->merge($user);
                $em->flush();
                $this->addFlash(
                    'notice', 'Your Profile has been updated successfully'
                );
            }
        }

        return $this->render('users/adduser.html.twig', [
            'page_title' => 'Edit User',
            'user_form' => $form->createView(),
            'back_btn_url' => $this->generateUrl('login_welcome'),
            'back_btn_text' => "Back to Dashboard"
        ]);
        

    }

    /**
     * @Route("/home/profile/password", name="update_profile_password")     
     */
    public function profilePassword(Request $request){         
        $user = $this->getDoctrine()->getRepository(User::class)->find($this->getUser()->getId()); 
        $form = $this->createForm(PasswordFormType::class, $user);

        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()){
            $em = $this->getDoctrine()->getManager();
            $em->merge($user);
            $em->flush();
            $this->addFlash('notice', 'Password Updated successfully');
            $this->redirectToRoute('user_profile');
            
        }

        return $this->render('users/adduser.html.twig', [
            'user_form' => $form->createView(),
            'back_btn_url' => $this->generateUrl('login_welcome'),
            'back_btn_text' => "Back to Dashboard",
            'page_title' => 'Edit Password',
        ]);
    }
     
}
