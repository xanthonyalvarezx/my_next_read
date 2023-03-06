<?php

namespace App\Controller;
//GENERAL

use App\Entity\Users;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;
// FORMS
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\TextType,EmailType, TelType, DateType;
// VALIDATION
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\Constraints\Type;
// Doctrine
use Doctrine\Persistence\ManagerRegistry;
use Doctrine\DBAL\DrverManager;
use APP\Entity\User;
// SESSIONS
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpFoundation\Session\Session;



class AuthController extends AbstractController
{
    private $requestStack;

    public function __construct(RequestStack $requestStack){
        $this->requestStack = $requestStack;
        
    }
    #[Route('/', name: 'login')]
    public function login(Request $request, ManagerRegistry $doctrine): Response
    {

        $form = $this->createFormBuilder()
        ->add('Email', TextType::class, array('attr' =>array('class' =>'form-control ')),[ 'constraints' => new NotBlank(),])

        ->add('Password', PasswordType::class, array('attr' =>array('class' => 'form-control  mb-3')),[ 'constraints' => new NotBlank(),])
        ->getForm();

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            // $form->getData() holds the submitted values
    
            $loginData = $form->getData();

            $sql = ("SELECT * FROM users WHERE email=?");
            $entityManager = $doctrine->getManager();
            $stmt = $entityManager->getConnection()->prepare($sql);
            $stmt->bindValue(1, $loginData['Email']);
            $query = $stmt->executeQuery();
            $results = $query->fetchAllAssociative();

            if($results && password_verify($loginData['Password'], $results[0]['password'])){
                $session =  $this->requestStack->getSession();
                $session->set('name', $results[0]['first_name'] . ' ' . $results[0]['last_name']);
                $session->set('email', $results[0]['email']);
                $session->set('id', $results[0]['id']);

                return $this->redirectToRoute('dashboard');
            }else{
                return $this->render('login.html.twig',[
                    'form' => $form->createView(),
                    'message' => 'Invalid email or password'

                ] );

            }

        }
        return $this->render('login.html.twig',[
            'form' => $form->createView(),
            'message' => ''
    ] );
}


   
    
    #[Route('/register', name: 'register')]
   /**
    * It takes the data from the form, creates a new user object, sets the data from the form to the
    * user object, and then persists the user object to the database.
    * 
    * @param Request request The request object.
    * @param ManagerRegistry doctrine The Doctrine service.
    * 
    * @return Response The form is being returned.
    */
    public function register(Request $request,ManagerRegistry $doctrine): Response{
        $form = $this->createFormBuilder()
        ->add('First_Name', TextType::class, array('attr' =>array('class' =>'form-control ')),[ 'constraints' => new NotBlank(),])
        ->add('Last_Name', TextType::class, array('attr' =>array('class' =>'form-control ')),[ 'constraints' => new NotBlank(),])
        ->add('Password', PasswordType::class, array('attr' =>array('class' => 'form-control  ')),[ 'constraints' => new NotBlank(),])
        ->add('Email', TextType::class, array('attr' =>array('class' =>'form-control mb-3')),[ 'constraints' => new NotBlank(),])
        ->getForm();
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            // $form->getData() holds the submitted values
    
            $userData = $form->getData();

            $entityManager = $doctrine->getManager();
            $user = new Users();

            $user->setFirstName($userData['First_Name']);
            $user->setLastName($userData['Last_Name']);
            $user->setEmail($userData['Email']);
            $user->setPassword(password_hash($userData['Password'], PASSWORD_DEFAULT));

            $entityManager->persist($user);
            $entityManager->flush();
           return  $this->redirectToRoute('login');
            
        }else{
            return $this->render('register.html.twig' ,[
                'form' => $form->createView(),
                'message' => 'Registration failed. Please try again.'
            ]);
            
        }
        
        return $this->render('register.html.twig' ,[
            'form' => $form->createView(),
            'message' => ''
        ]);
        
    }

    #[Route('/logout', name: 'logout')]

    public function logout()
    {
        $session =  $this->requestStack->getSession();
        $session->remove('name');
        $session->remove('email');
        $session->remove('id');

        return $this->redirectToRoute('login');
    }
}





