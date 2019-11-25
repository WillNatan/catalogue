<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\adminEditType;
use App\Form\adminUserType;
use App\Form\UserEditType;
use App\Form\UserType;
use App\Repository\UserRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Authentication\Token\UsernamePasswordToken;
use Symfony\Component\Security\Core\Authorization\AccessDecisionManagerInterface;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

/**
 * @Route("dashboard/utilisateurs")
 */
class UserController extends AbstractController
{
    /**
     * @Route("/", name="Utilisateurs", methods={"GET"})
     */
    public function index(AccessDecisionManagerInterface $accessDecisionManager, UserRepository $userRepository): Response
    {

        $user= $this->getUser();

        $token = new UsernamePasswordToken($user, 'none', 'none', $user->getRoles());
        if (!$accessDecisionManager->decide($token, ['ROLE_ADMIN'])) {
            return $this->redirectToRoute('Administration');
        };
        return $this->render('user/index.html.twig', [
            'users' => $userRepository->findAll(),

        ]);
    }


    /**
     * @Route("/ajout-utilisateur", name="Nouvel-utilisateur", methods={"GET","POST"})
     */
    public function new(AccessDecisionManagerInterface $accessDecisionManager, Request $request, UserPasswordEncoderInterface $encoder): Response
    {
        $user= $this->getUser();

        $token = new UsernamePasswordToken($user, 'none', 'none', $user->getRoles());
        if (!$accessDecisionManager->decide($token, ['ROLE_SUPER_ADMIN'])) {
            return $this->redirectToRoute('Administration');
        };

        $user = new User();
        $form = $this->createForm(adminUserType::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $encodedPassword = $encoder->encodePassword($user,$user->getPlainPassword());
            $user->setPassword($encodedPassword);
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($user);
            $entityManager->flush();

            return $this->redirectToRoute('Utilisateurs');
        }

        return $this->render('user/new.html.twig', [
            'user' => $user,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}", name="Détails-utilisateur", methods={"GET"})
     */
    public function show(Request $request,User $user): Response
    {
        if($this->getUser()->getId() == $request->get('id')){
           return $this->redirectToRoute('Administration',['id'=>$this->getUser()->getId()]);
        }
            return $this->render('user/show.html.twig', [
                'user' => $user,
            ]);
    }

    /**
     * @Route("/{id}/modifier-utilisateur", name="Modifier-utilisateur", methods={"GET","POST"})
     */
    public function edit(Request $request, User $user, UserPasswordEncoderInterface $encoder): Response
    {
        $form = $this->createForm(adminEditType::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            if($encoder->isPasswordValid($user,$form->get('oldPassword')->getData())){
                $newencodedPassword = $encoder->encodePassword($user, $user->getPlainPassword());
                $user->setPassword($newencodedPassword);
                $this->getDoctrine()->getManager()->flush();
                return $this->redirectToRoute('Utilisateurs');
            }

        }

        return $this->render('user/edit.html.twig', [
            'user' => $user,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}", name="Suppression-utilisateur", methods={"DELETE"})
     */
    public function delete(Request $request, User $user): Response
    {
        if ($this->isCsrfTokenValid('delete'.$user->getId(), $request->request->get('_token'))) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->remove($user);
            $entityManager->flush();
        }

        return $this->redirectToRoute('Utilisateurs');
    }
}
