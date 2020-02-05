<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\firstLoginType;
use App\Repository\ReportCatalogRepository;
use App\Repository\UserRepository;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

class FrontController extends AbstractController
{
    /**
     * @Route("/", name="Main")
     */
    public function index(ReportCatalogRepository $catalogRepository, UserRepository $userRepository)
    {
        if(!empty($userRepository->findAll()))
        {
            return $this->redirectToRoute('Administration');
        }
        else{
            return $this->redirectToRoute('first-login');
        }

    }

    /**
     * @Route("/premiere-connexion", name="first-login")
     */
    public function firstLogin(ReportCatalogRepository $catalogRepository, Request $request, UserPasswordEncoderInterface $passwordEncoder)
    {

        $user = new User();
        $form = $this->createForm(firstLoginType::class, $user);
        $form->handleRequest($request);


        if ($form->isSubmitted() && $form->isValid()) {
            $encodedPassword = $passwordEncoder->encodePassword($user, $user->getPlainPassword());
            $user->setPassword($encodedPassword);
            $user->setRoles(['ROLE_USER','ROLE_ADMIN']);
            $em = $this->getDoctrine()->getManager();
            $em->persist($user);
            $em->flush();
            return $this->redirectToRoute('Administration');
        }
        return $this->render('front/firstreg.html.twig', [
            'form'=>$form->createView()
        ]);
    }
}
