<?php

namespace App\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use App\Entity\User; 
use App\Form\UserType;
use App\Form\ProfileFormType;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

class UsersController extends AbstractController{
    /**
     * @Route("/home/users", name="list_users")
     */
    public function index()
    {
        return $this->render('users/index.html.twig', [
            'controller_name' => 'List All Users',
        ]);
    }

    /**
     * @Route("/home/users/add", name="add_user")
     */
    public function addNewUser(Request $request, UserPasswordEncoderInterface $passwordEncoder)
    {
        $user = new User();
        $form = $this->createForm(UserType::class, $user);

        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()){
            $em = $this->getDoctrine()->getManager();
            $em->persist($user);
            $em->flush();
            $userId = $user->getId();
            return $this->redirectToRoute('edit_user', ["user" => $userId]);
        }
        

        return $this->render('users/adduser.html.twig', [
            'page_title' => 'Add New User',
            'user_form' => $form->createView(),
            'back_btn_url' => $this->generateUrl('list_users'),
            'back_btn_text' => "Back to List Page",
        ]);
    }

    /**
     * @Route("/home/users/{user}", name="edit_user")
     */
    public function editUser(Request $request){
        
        $userId = $request->get("user");
        $user = $this->getDoctrine()->getRepository(User::class)->find($userId); 

        if(!$user){
            return $this->redirect('list_users');
        }

        $form = $this->createForm(ProfileFormType::class, $user);

        if ($request->isMethod('POST')) {
            $form->handleRequest($request);
            
            if($form->isSubmitted() && $form->isValid()){                     
                $em = $this->getDoctrine()->getManager();            
                $em->merge($user);
                $em->flush();
            }
        }

        return $this->render('users/adduser.html.twig', [
            'page_title' => 'Edit User',
            'user_form' => $form->createView(),
            'back_btn_url' => $this->generateUrl('list_users'),
            'back_btn_text' => "Back to List Page"
        ]);
    }
}
