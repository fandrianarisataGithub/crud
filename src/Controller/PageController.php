<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\UserType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

class PageController extends AbstractController
{
    private $passwordEncoder;
    // constructeur
    public function __construct(UserPasswordEncoderInterface $passwordEncoder)
    {
        $this->passwordEncoder = $passwordEncoder;
    }
    /**
     * @Route("/page", name="page")
     */
    public function index(): Response
    {
        return $this->render('page/index.html.twig', [
            'controller_name' => 'PageController',
        ]);
    }
    /**
     * @Route("/", name="first")
     */
    public function first(): Response
    {
        return $this->redirectToRoute("app_login");
    }   
    /**
     * @Route("/register_user", name="user.register")
     */
    public function register_user(Request $request, EntityManagerInterface $manager)
    {
        $user = new User();
        $form = $this->createForm(UserType::class, $user);
        $form->handleRequest($request);
        if($form->isSubmitted() && $form->isValid()){
            $user = $form->getData();
           
            $r = $user->getRoles()[0];
            $groupe = "";
            if($form->get('groupe')->getData()==0){
                $groupe = "Annonceur";
            }
            else if($form->get('groupe')->getData()!=0){
                $groupe = "Non annonceur";
            }
            if($r == 0){
                $user->setRoles(['ROLE_USER']);
            }
            else if($r == 1){
                $user->setRoles(['ROLE_ADMIN']);
            }
           
            $user->setGroupe($groupe);
            // encodage de password ;
            $user->setPassword($this->passwordEncoder->encodePassword($user, $form->get('password')->getData()));
            $manager->persist($user);
            $manager->flush();
        }
        return $this->render('page/register_user.html.twig',[
            'form_register'     => $form->createView(),
        ]);
    }
    /**
     * @Route("/profile/home", name="home")
     */
    public function home(Request $request)
    {
        
        return $this->render('page/home.html.twig', [

        ]);
    }
}
