<?php

namespace App\Controller;

use Symfony\Component\HttpClient\HttpClient;
use Symfony\Contracts\HttpClient\HttpClientInterface;
//GENERAL

use App\Entity\Users;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;
// FORMS
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\{
    TextType,
    EmailType,
    TelType,
    DateType,
    ChoiceType
};
// VALIDATION
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\Constraints\Type;
// Doctrine
use Doctrine\Persistence\ManagerRegistry;
use Doctrine\DBAL\DrverManager;
use APP\Entity\User;
use PDO;
// SESSIONS
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpFoundation\Session\Session;

class PagesController extends AbstractController
{
    private $client;
    private $requestStack;

    public function __construct(HttpClientInterface $client, RequestStack $requestStack)
    {
        $this->client = $client;
        $this->requestStack = $requestStack;
    }


    #[Route('/dashboard', name: 'dashboard')]
    public function index(Request $request, RequestStack $requestStack)
    {
        $session = $this->requestStack->getSession();

        if ($session->has('id')) {
            $name = $session->get('name');

            return $this->render('pages/dashboard.html.twig', [
                
                'name' => $name,
            ]);
        } else {
            return $this->redirectToRoute('login');
        }
    }

    #[Route('/library', name: 'library')]
    public function getLibrary(Request $request, RequestStack $requestStack, ManagerRegistry $doctrine)
    {
        $session = $this->requestStack->getSession();
        $entityManager = $doctrine->getManager();
        if ($session->has('id')) {
            $name = $session->get('name');
            $id = $session->get('id');

          
            return $this->render('pages/library.html.twig', [
                
                'name' => $name,
                
            ]);
        } else {
            return $this->redirectToRoute('login');
        }
    }
}
