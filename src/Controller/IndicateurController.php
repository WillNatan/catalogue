<?php

namespace App\Controller;

use App\Entity\Indicateur;
use App\Form\IndicateurType;
use App\Repository\IndicateurRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/indicateur")
 */
class IndicateurController extends AbstractController
{
    /**
     * @Route("/", name="indicateur_index", methods={"GET"})
     */
    public function index(IndicateurRepository $indicateurRepository): Response
    {
        return $this->render('indicateur/index.html.twig', [
            'indicateurs' => $indicateurRepository->findAll(),
        ]);
    }

    /**
     * @Route("/new", name="indicateur_new", methods={"GET","POST"})
     */
    public function new(Request $request): Response
    {
        $indicateur = new Indicateur();
        $form = $this->createForm(IndicateurType::class, $indicateur);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($indicateur);
            $entityManager->flush();

            return $this->redirectToRoute('indicateur_index');
        }

        return $this->render('indicateur/new.html.twig', [
            'indicateur' => $indicateur,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}", name="indicateur_show", methods={"GET"})
     */
    public function show(Indicateur $indicateur): Response
    {
        return $this->render('indicateur/show.html.twig', [
            'indicateur' => $indicateur,
        ]);
    }

    /**
     * @Route("/{id}/edit", name="indicateur_edit", methods={"GET","POST"})
     */
    public function edit(Request $request, Indicateur $indicateur): Response
    {
        $form = $this->createForm(IndicateurType::class, $indicateur);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->getDoctrine()->getManager()->flush();

            return $this->redirectToRoute('indicateur_index');
        }

        return $this->render('indicateur/edit.html.twig', [
            'indicateur' => $indicateur,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}", name="indicateur_delete", methods={"DELETE"})
     */
    public function delete(Request $request, Indicateur $indicateur): Response
    {
        if ($this->isCsrfTokenValid('delete'.$indicateur->getId(), $request->request->get('_token'))) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->remove($indicateur);
            $entityManager->flush();
        }

        return $this->redirectToRoute('indicateur_index');
    }
}
