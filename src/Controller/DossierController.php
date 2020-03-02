<?php

namespace App\Controller;

use App\Entity\Domaines;
use App\Form\DossierType;
use App\Repository\DossierRepository;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Doctrine\Common\Collections\ArrayCollection;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

/**
 * @Route("dashboard/domaines")
 */
class DossierController extends AbstractController
{
    /**
     * @Route("/", name="Dossiers", methods={"GET"})
     */
    public function index(DossierRepository $dossierRepository): Response
    {
        return $this->render('dossier/index.html.twig', [
            'dossiers' => $dossierRepository->findAll(),
        ]);
    }

    /**
     * @Route("/nouveau-domaine", name="Nouveau-dossier", methods={"GET","POST"})
     */
    public function new(Request $request): Response
    {
        $dossier = new Domaines();
        $subfolders = new ArrayCollection();

        foreach ($dossier->getSubFolders() as $subfolder){
            $subfolders->add($subfolder);
        }

        $form = $this->createForm(DossierType::class, $dossier);
        $form->handleRequest($request);

        
        if ($form->isSubmitted() && $form->isValid()) {

            $entityManager = $this->getDoctrine()->getManager();
            foreach($subfolders as $subfolder){
                if($dossier->getSubFolders()->contains() === false){
                    $entityManager->remove($subfolder);
                }
            }
    
            $entityManager->persist($dossier);
            $entityManager->flush();

            return $this->redirectToRoute('Dossiers');
        }

        return $this->render('dossier/new.html.twig', [
            'dossier' => $dossier,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}", name="Détails-dossier", methods={"GET"})
     */
    public function show(Domaines $dossier): Response
    {
        return $this->render('dossier/show.html.twig', [
            'dossier' => $dossier,
        ]);
    }

    /**
     * @Route("/{id}/modifier-domaine", name="Modifier-dossier", methods={"GET","POST"})
     */
    public function edit(Request $request, Domaines $dossier): Response
    {
        $form = $this->createForm(DossierType::class, $dossier);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->getDoctrine()->getManager()->flush();

            return $this->redirectToRoute('Dossiers');
        }

        return $this->render('dossier/edit.html.twig', [
            'dossier' => $dossier,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}", name="Suppression-dossier", methods={"DELETE"})
     */
    public function delete(Request $request, Domaines $dossier): Response
    {
        if ($this->isCsrfTokenValid('delete'.$dossier->getId(), $request->request->get('_token'))) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->remove($dossier);
            $entityManager->flush();
        }

        return $this->redirectToRoute('Dossiers');
    }
}
