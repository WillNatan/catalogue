<?php

namespace App\Controller;

use App\Entity\Liens;
use App\Entity\Matrice;
use App\Form\MatriceType;
use App\Repository\MatriceRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/dashboard/matrice")
 */
class MatriceController extends AbstractController
{
    /**
     * @Route("/", name="matrice_index", methods={"GET"})
     */
    public function index(MatriceRepository $matriceRepository): Response
    {
        return $this->render('matrice/index.html.twig', [
            'matrices' => $matriceRepository->findAll(),
        ]);
    }

    /**
     * @Route("/new", name="matrice_new", methods={"GET","POST"})
     */
    public function new(Request $request): Response
    {
        $matrice = new Matrice();
        $liens = new ArrayCollection();
        foreach($matrice->getLiens() as $lien)
        {
            $liens->add($lien);
        }

        $form = $this->createForm(MatriceType::class, $matrice);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager = $this->getDoctrine()->getManager();
            foreach($liens as $lien)
            {
                if($matrice->getLiens()->contains()=== false)
                {
                    $entityManager->remove($lien);
                }
            }


            $entityManager->persist($matrice);
            $entityManager->flush();

            return $this->redirectToRoute('matrice_index');
        }

        return $this->render('matrice/new.html.twig', [
            'matrice' => $matrice,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}", name="matrice_show", methods={"GET"})
     */
    public function show(Matrice $matrice): Response
    {
        return $this->render('matrice/show.html.twig', [
            'matrice' => $matrice,
        ]);
    }

    /**
     * @Route("/axes/{id}", name="axes_show", methods={"GET"})
     */
    public function axes(Liens $lien): Response
    {
        return $this->render('matrice/axes.html.twig', [
            'lien' => $lien,
        ]);
    }

    /**
     * @Route("/{id}/edit", name="matrice_edit", methods={"GET","POST"})
     */
    public function edit(Request $request, Matrice $matrice): Response
    {
        $form = $this->createForm(MatriceType::class, $matrice);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->getDoctrine()->getManager()->flush();

            return $this->redirectToRoute('matrice_index');
        }

        return $this->render('matrice/edit.html.twig', [
            'matrice' => $matrice,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}", name="matrice_delete", methods={"DELETE"})
     */
    public function delete(Request $request, Matrice $matrice): Response
    {
        if ($this->isCsrfTokenValid('delete'.$matrice->getId(), $request->request->get('_token'))) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->remove($matrice);
            $entityManager->flush();
        }

        return $this->redirectToRoute('matrice_index');
    }
}
