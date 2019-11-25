<?php

namespace App\Controller;

use App\Entity\ReportCatalog;
use App\Form\ReportCatalogType;
use App\Repository\DossierRepository;
use App\Repository\ReportCatalogRepository;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

/**
 * @Route("/admin/catalogue-rapports-bo")
 */
class ReportCatalogController extends AbstractController
{
    /**
     * @Route("/", name="Catalogue-des-rapports", methods={"GET"})
     */
    public function index(ReportCatalogRepository $reportCatalogRepository): Response
    {
        return $this->render('report_catalog/index.html.twig', [
            'report_catalogs' => $reportCatalogRepository->findAll(),
        ]);
    }

    /**
     * @Route("/nouveau", name="Nouveau-rapport", methods={"GET","POST"})
     */
    public function new(Request $request, ReportCatalogRepository $reportCatalogRepository): Response
    {

        $reportCatalog = new ReportCatalog();

        $form = $this->createForm(ReportCatalogType::class, $reportCatalog);
        $form->handleRequest($request);
        
        
        if ($form->isSubmitted() && $form->isValid()) {

            if(!$reportCatalogRepository->findAll()){
                $reportCatalog->setN(1);
            }else{
                $reportCatalog->setN(count($reportCatalogRepository->findAll())+1);
            }
            $reportCatalog->setUser($this->getUser());
            $reportCatalog->setLastUpdate(new \DateTime('now'));
            $reportCatalog->setUpdateNb(0);
            $reportCatalog->setMainFolder($form->getData()->getMainFolder());
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($reportCatalog);
            $entityManager->flush();

            return $this->redirectToRoute('Catalogue-des-rapports');
        }

        return $this->render('report_catalog/new.html.twig', [
            'report_catalog' => $reportCatalog,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}", name="Détails-rapport", methods={"GET"})
     */
    public function show(ReportCatalog $reportCatalog): Response
    {
        return $this->render('report_catalog/show.html.twig', [
            'report_catalog' => $reportCatalog,
        ]);
    }

    /**
     * @Route("/{id}/modifier-rapport", name="Modifier-rapport", methods={"GET","POST"})
     */
    public function edit(Request $request, ReportCatalog $reportCatalog, DossierRepository $dossierRepository): Response
    {
        $nbUpdate = $reportCatalog->getUpdateNb();
        $form = $this->createForm(ReportCatalogType::class, $reportCatalog);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $reportCatalog->setLastUpdate(new \DateTime('now'));
            $reportCatalog->setUpdateNb($nbUpdate+1);
            $this->getDoctrine()->getManager()->flush();

            return $this->redirectToRoute('Catalogue-des-rapports');
        }

        return $this->render('report_catalog/edit.html.twig', [
            'report_catalog' => $reportCatalog,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}", name="Suppression-rapport", methods={"DELETE"})
     */
    public function delete(Request $request, ReportCatalog $reportCatalog): Response
    {
        if ($this->isCsrfTokenValid('delete'.$reportCatalog->getId(), $request->request->get('_token'))) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->remove($reportCatalog);
            $entityManager->flush();
        }

        return $this->redirectToRoute('Catalogue-des-rapports');
    }
}
