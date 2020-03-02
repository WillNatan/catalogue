<?php

namespace App\Controller;

use App\Entity\ReferentielObjets;
use App\Entity\RefObjRapport;
use App\Entity\Reports;
use App\Form\ReportCatalogType;
use App\Form\SQLTextType;
use App\Repository\DossierRepository;
use App\Repository\ReportCatalogRepository;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Security\Core\Authentication\Token\UsernamePasswordToken;
use Symfony\Component\Security\Core\Authorization\AccessDecisionManager;
use Symfony\Component\Security\Core\Authorization\AccessDecisionManagerInterface;

/**
 * @Route("/dashboard/catalogue-rapports-bo")
 */
class ReportCatalogController extends AbstractController
{
    /**
     * @Route("/", name="Catalogue-des-rapports", methods={"GET"})
     */
    public function index(ReportCatalogRepository $reportCatalogRepository): Response
    {
        return $this->render('report_catalog/index.html.twig', [
            'report_catalogs' => $reportCatalogRepository->findAll()
        ]);
    }

    /**
     * @Route("/nouveau", name="Nouveau-rapport", methods={"GET","POST"})
     */
    public function new(AccessDecisionManagerInterface $accessDecisionManager, Request $request, ReportCatalogRepository $reportCatalogRepository): Response
    {
        $user= $this->getUser();
        $token = new UsernamePasswordToken($user, 'none', 'none', $user->getRoles());
        if (!$accessDecisionManager->decide($token, ['ROLE_SUPER_ADMIN'])) {
            return $this->redirectToRoute('Administration');
        };
        $reportCatalog = new Reports();
        $form = $this->createForm(ReportCatalogType::class, $reportCatalog);
        $form->handleRequest($request);
        
        
        if ($form->isSubmitted() && $form->isValid()) {

            if(!$reportCatalogRepository->findAll()){
                $reportCatalog->setN(1);
            }else{
                $reportCatalog->setN(count($reportCatalogRepository->findAll())+1);
            }
            $reportCatalog->setCreatedBy($this->getUser());
            $reportCatalog->setUpdatedBy($this->getUser());
            $reportCatalog->setLastUpdate(new \DateTime('now'));
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
     * @Route("/{id}", name="Détails-rapport", methods={"GET","POST"})
     */
    public function show(Reports $reportCatalog, Request $request): Response
    {
        $form = $this->createForm(SQLTextType::class, $reportCatalog);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $pattern='/[a-zA-Z_0-9]+\.+[a-zA-Z_0-9]+\.+[a-zA-Z_0-9]+/';
            preg_match_all($pattern, $reportCatalog->getSqltext(), $match);
            $matches = array_unique($match[0]);
            $allrefs = $this->getDoctrine()->getRepository(RefObjRapport::class)->findBy(['nomRapport'=>$reportCatalog->getNomRapport()]);
            $em = $this->getDoctrine()->getManager();
            foreach ($allrefs as $ref)
            {
                $em->remove($ref);
                $em->flush();
            }
            foreach ($matches as $m)
            {
                if($this->getDoctrine()->getRepository(ReferentielObjets::class)->findOneBy(['nomObjet'=>$m]))
                {
                    $objet =$this->getDoctrine()->getRepository(ReferentielObjets::class)->findOneBy(['nomObjet'=>$m]);
                    if($this->getDoctrine()->getRepository(RefObjRapport::class)->findOneBy(['rapport'=>$reportCatalog, 'objet'=>$objet])){
                        $split = explode('.', $m, 3);
                        $objet->setSchemaObj($split[0]);
                        $objet->setTableobj($split[1]);
                        $objet->setChamp($split[2]);
                        $em->flush();
                    }else{
                        $referentiel = new RefObjRapport();
                        $referentiel->setObjet($objet);
                        $referentiel->setRapport($reportCatalog);
                        $referentiel->setNomObjet($m);
                        $referentiel->setNomRapport($reportCatalog->getNomRapport());
                        $em->persist($referentiel);
                        $em->flush();
                    }
                }
                else{
                    $objet = new ReferentielObjets();
                    $objet->setNomObjet(strtoupper($m));
                    $split = explode('.', $m, 3);
                    $objet->setSchemaObj($split[0]);
                    $objet->setTableobj($split[1]);
                    $objet->setChamp($split[2]);
                    $referentiel = new RefObjRapport();
                    $referentiel->setObjet($objet);
                    $referentiel->setRapport($reportCatalog);
                    $referentiel->setNomObjet($m);
                    $referentiel->setNomRapport($reportCatalog->getNomRapport());
                    $em->persist($referentiel);
                    $em->persist($objet);
                    $em->flush();
                }
            }
            return $this->redirectToRoute('Détails-rapport', ['id'=>$request->get('id')]);
        }

        return $this->render('report_catalog/show.html.twig', [
            'report_catalog' => $reportCatalog,
            'form'=>$form->createView()
        ]);
    }

    /**
     * @Route("/{id}/modifier-rapport", name="Modifier-rapport", methods={"GET","POST"})
     */
    public function edit(AccessDecisionManagerInterface $accessDecisionManager, Request $request, Reports $reportCatalog, DossierRepository $dossierRepository): Response
    {
        $user= $this->getUser();
        $token = new UsernamePasswordToken($user, 'none', 'none', $user->getRoles());
        if (!$accessDecisionManager->decide($token, ['ROLE_SUPER_ADMIN'])) {
            return $this->redirectToRoute('Administration');
        };
        $nbUpdate = $reportCatalog->getUpdateNb();
        $form = $this->createForm(ReportCatalogType::class, $reportCatalog);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $reportCatalog->setLastUpdate(new \DateTime('now'));
            $reportCatalog->setUpdateNb($nbUpdate+1);
            $reportCatalog->setUpdatedBy($this->getUser());
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
    public function delete(Request $request, Reports $reportCatalog): Response
    {
        if ($this->isCsrfTokenValid('delete'.$reportCatalog->getId(), $request->request->get('_token'))) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->remove($reportCatalog);
            $entityManager->flush();
        }

        return $this->redirectToRoute('Catalogue-des-rapports');
    }
}
