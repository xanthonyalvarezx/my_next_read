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



class DataController extends AbstractController
{

    private $requestStack;

    public function __construct(RequestStack $requestStack){
        $this->requestStack = $requestStack;
        
    }
    #[Route('/save/collection', name: 'save_collection')]
    public function saveBooks(Request $request, ManagerRegistry $doctrine, RequestStack $requestStack):Response{
        $session = $this->requestStack->getSession();
        $id = $session->get('id');
        $body = $request->getContent();
        $entityManager = $doctrine->getManager();
       

        $sql = ("SELECT collection FROM libraries WHERE user_id = ? ");
            $stmt = $entityManager->getConnection()->prepare($sql);
            $stmt->bindValue(1,$id);
            $query = $stmt->executeQuery();
            $collection = $query->fetchAll();

            if(!$collection){
                $sql2 = "INSERT INTO libraries(user_id, collection) VALUES(?,?)";
                $stmt = $entityManager->getConnection()->prepare($sql2);
                $stmt->bindValue(1,$id);
                $stmt->bindValue(2,$body);
                $query = $stmt->executeQuery();
            }else{
                $body = array_merge($collection, json_decode($body));
               $body =  json_encode($body);
                $sql3 = "UPDATE libraries SET collection =? WHERE user_id = $id";
                $stmt = $entityManager->getConnection()->prepare($sql3);
                $stmt->bindValue(1,$body);
                $query = $stmt->executeQuery();
            }

        return $this->json(["success"=> "success"]);

    }

    
    #[Route('/collection', name: 'get_collection')]
    public function getCollection(Request $request, ManagerRegistry $doctrine, RequestStack $requestStack):Response{
        $session = $this->requestStack->getSession();
        $id = $session->get('id');
       
        $entityManager = $doctrine->getManager();
       

        $sql = ("SELECT collection FROM libraries WHERE user_id = ? ");
            $stmt = $entityManager->getConnection()->prepare($sql);
            $stmt->bindValue(1,$id);
            $query = $stmt->executeQuery();
            $collection = $query->fetchAll();

        return $this->json($collection);

    }










}