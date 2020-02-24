<?php

namespace App\Controller;

use App\Entity\ReferentielObjets;
use App\Form\ObjectDenominationType;
use App\Form\ObjectType;
use App\Repository\LiensRepository;
use App\Repository\ReferentielObjetsRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Authorization\AccessDecisionManagerInterface;

/**
 * @Route("/dashboard")
 */
class ObjetsController extends AbstractController
{
    /**
     * @Route("/objets", name="objets")
     */
    public function objets(Request $request, ReferentielObjetsRepository $objetsRepository)
    {

        return $this->render('objects/ref.html.twig', ['refetentiels'=>$objetsRepository->findAll()]);
    }

    /**
     * @Route("/nouvel-objet", name="newobject")
     */
    public function newObject(Request $request)
    {
        $objet = new ReferentielObjets();
        $form = $this->createForm(ObjectDenominationType::class, $objet);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($objet);
            $em->flush();
            return $this->redirectToRoute('objets');
        }
        return $this->render('objects/new.html.twig',
            [
                'form'=>$form->createView()
            ]);
    }


    /**
     * @Route("/getObjectDetails/{id}", name="getObjectsDetails", methods={"POST"})
     */
    public function getObjectsDetails(ReferentielObjets $objets, LiensRepository $liensRepository)
    {
        if(!is_null($liensRepository->findOneBy(['Indicateur'=>$objets])))
        {
            return new JsonResponse([
                'description'=>$objets->getDescription(),
                'type'=>$objets->getType(),
                'qualification'=>'disabled',
                'defaultQual'=>$objets->getQualification(),
                'denomination'=>$objets->getDenomination(),
            ]);
        }
        else{
            return new JsonResponse([
                'description'=>$objets->getDescription(),
                'type'=>$objets->getType(),
                'qualification'=>$objets->getQualification(),
                'denomination'=>$objets->getDenomination()
            ]);
        }
    }

    /**
     * @Route("/objets/{id}", name="objets_details")
     */
    public function detailsObjets(Request $request,AccessDecisionManagerInterface $accessDecisionManager, ReferentielObjets $objets)
    {
        $form = $this->createForm(ObjectType::class, $objets);
        $form->handleRequest($request);

        if($request->isMethod('post')){
            $object = $request->request->all();
            $objets->setDescription($request->request->get('description'));
            $objets->setType($request->request->get('type'));
            $objets->setDenomination($request->request->get('denomination'));
            $objets->setQualification($request->request->get('qualification'));
            $this->getDoctrine()->getManager()->flush();
            return new RedirectResponse($request->headers->get('referer'));
        }

        if ($form->isSubmitted() && $form->isValid()) {
            $this->getDoctrine()->getManager()->flush();
            return new RedirectResponse($request->headers->get('referer'));
        }

        return $this->render('objects/show.html.twig', [
            'objets'=>$objets,
            'form'=>$form->createView()
        ]);
    }

}
