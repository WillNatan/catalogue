<?php

namespace App\Controller;

use App\Entity\Dossier;
use App\Entity\Imports;
use App\Entity\ReferentielObjets;
use App\Entity\ReportCatalog;
use App\Entity\SousDossier;
use App\Entity\User;
use App\Form\adminEditType;
use App\Form\EditPasswordType;
use App\Form\ImportType;
use App\Form\UserEditType;
use App\Repository\DossierRepository;
use App\Repository\ImportsRepository;
use App\Repository\ReferentielObjetsRepository;
use App\Repository\ReportCatalogRepository;
use App\Repository\SousDossierRepository;
use Doctrine\ORM\EntityManagerInterface;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\ResponseHeaderBag;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Validator\Constraints\Json;



/**
 * @Route("/dashboard")
 */
class AdminController extends AbstractController
{



    /**
     * @Route("/", name="Administration")
     */
    public function index(ReportCatalogRepository $catalogRepository, EntityManagerInterface $em, Request $request)
    {
        $allReports = $catalogRepository->findAll();
        $nbRapport = count($allReports);
        $lastReport = $catalogRepository->findOneBy([],['id'=>'DESC']);
        $nbUpdate = 0;

        $qbReports = $em->getRepository(ReportCatalog::class)->createQueryBuilder('r')
            ->leftJoin('r.mainFolder', 'p')
            ->addSelect('p')
            ->select('COUNT(r) AS nombreRapport','p.nomDossier')
            ->groupBy('p.nomDossier')
            ->getQuery()
            ->getArrayResult();

        $qbReportsByUser = $em->getRepository(ReportCatalog::class)->createQueryBuilder('r')
            ->leftJoin('r.createdBy', 'p')
            ->addSelect('p')
            ->select('COUNT(r) AS nombreRapport','p.username')
            ->groupBy('p.username')
            ->getQuery()
            ->getArrayResult();

        $qbRecette = $em->getRepository(ReportCatalog::class)->createQueryBuilder('r')
            ->leftJoin('r.mainFolder','p')
            ->addSelect('p')
            ->select('r.id','r.Nom_Rapport','p.nomDossier','r.VersionActuelle','r.CreationDate','r.n')
            ->where('p.nomDossier = :folder')
            ->setParameter('folder','RECETTE')
            ->groupBy('r.id','r.Nom_Rapport','p.nomDossier','r.VersionActuelle','r.CreationDate','r.n');
        $recette = $qbRecette->getQuery()->getArrayResult();

        $qbProd = $em->getRepository(ReportCatalog::class)->createQueryBuilder('r')
            ->leftJoin('r.mainFolder','p')
            ->addSelect('p')
            ->select('r.id','r.Nom_Rapport','p.nomDossier','r.VersionActuelle','r.CreationDate','r.n')
            ->where('p.nomDossier != :folder')
            ->setParameter('folder','RECETTE')
            ->groupBy('r.id','r.Nom_Rapport','p.nomDossier','r.VersionActuelle','r.CreationDate','r.n');
        $prod = $qbProd->getQuery()->getArrayResult();


        foreach($allReports as $report){
            $nbUpdate = $nbUpdate + $report->getUpdateNb();
        }


        if($request->isMethod('post'))
        {
            $user = $this->getUser();
            $user->setResponseSecrete($request->request->get('reponseSecrete'));
            $user->setQuestionSecrete($request->request->get('questionSecrete'));
            $this->getDoctrine()->getManager()->flush();
            return $this->redirectToRoute('Administration');
        }

        return $this->render('admin/index.html.twig',['nbRapport'=>$nbRapport, 'nbUpdate'=>$nbUpdate, 'lastReport'=>$lastReport, 'production'=>$prod, 'recette'=>$recette, 'reportsByFolder'=>$qbReports, 'reportByUser'=>$qbReportsByUser]);
    }

    /**
     * @Route("/profil", name="Profil")
     */
    public function profile()
    {
        return $this->render('admin/profile.html.twig');
    }


    /**
     * @Route("/objets", name="objets")
     */
    public function objets(ReferentielObjetsRepository $objetsRepository)
    {

        return $this->render('admin/ref.html.twig', ['refetentiels'=>$objetsRepository->findAll()]);
    }

    /**
     * @Route("/objets/{id}", name="objets_details")
     */
    public function detailsObjets(ReferentielObjets $objets)
    {
        return $this->render('objects/show.html.twig', ['objets'=>$objets]);
    }

    /**
     * @Route("e/{id}", name="edit_object", methods={"GET", "POST"})
     */
    public function editObject(ReferentielObjets $objets, Request $request)
    {
        if($request->isMethod("POST"))
        {
            $description = $request->request->get('description');
            $type = $request->request->get('type');
            $qualification = $request->request->get('qualifier');

            $objets->setDescription($description);
            $objets->setType($type);
            $objets->setQualification($qualification);
            $this->getDoctrine()->getManager()->flush();

            return new RedirectResponse($request->headers->get('referer'));
        }
        else
            {
            return $this->redirectToRoute("Administration");
        }
    }

    /**
     * @Route("/profil/{id}", name="Modifier-Profil")
     */
    public function editProfile(Request $request, UserPasswordEncoderInterface $encoder, UserInterface $user, EntityManagerInterface $entityManager)
    {
        $currentPw = $user->getPassword();
        $form = $this->createForm(UserEditType::class, $user);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $user->setPassword($currentPw);
            $this->getDoctrine()->getManager()->flush();
            return $this->redirectToRoute('app_logout');
        }


        return $this->render('admin/editProfile.html.twig', [
            'user' => $user,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/modifier-mot-de-passe", name="Modifier-mot-de-passe")
     */
    public function editPassword(Request $request, UserPasswordEncoderInterface $encoder)
    {
        $user = $this->getUser();
        $form = $this->createForm(EditPasswordType::class, $user);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            if($encoder->isPasswordValid($user,$form->get('oldPassword')->getData())){
                $newencodedPassword = $encoder->encodePassword($user, $user->getPlainPassword());
                $user->setPassword($newencodedPassword);
                $this->getDoctrine()->getManager()->flush();
                return $this->redirectToRoute('app_logout');
            }else{
                $this->addFlash('err',"L'ancien mot de passe ne correspond pas");
                return $this->redirectToRoute('Modifier-mot-de-passe');
            }
        }
        return $this->render('admin/editPassword.html.twig', [
            'user' => $user,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/excel", name="spreadsheet")
     * @throws \PhpOffice\PhpSpreadsheet\Exception
     */
    public function excel(EntityManagerInterface $entityManager, ReportCatalogRepository $catalogRepository){
        $user = $this->getUser();
        if($user) {
            $spreadsheet = new Spreadsheet();
            $qb = $entityManager->getRepository(ReportCatalog::class)->createQueryBuilder('r');
            $qb
                ->leftJoin('r.mainFolder', 'p')
                ->addSelect('p')
                ->leftJoin('r.subFolder', 'sf')
                ->addSelect('sf')
                ->select('r.n', 'p.nomDossier', 'sf.nomDossier as subfolderName', 'r.Nom_Rapport', 'r.VersionActuelle', 'r.Commentaire', 'r.Categorie', 'r.Objectifs', 'r.Details', 'r.Sources', 'r.Parametres', 'r.Historique_Versions');
            $res = $qb->getQuery()->getArrayResult();


            $sheet = $spreadsheet->getActiveSheet();
            $sheet->getRowDimension('1')->setRowHeight(37.5);
            $nCols = 12; //set the number of columns

            foreach (range(0, $nCols) as $col) {
                $sheet->getColumnDimensionByColumn($col)->setAutoSize(true);
            }
            $sheet->getStyle('A1:L1')->getAlignment()->setHorizontal('center');
            $sheet->getStyle('A1:L1')->getAlignment()->setVertical('center');
            $sheet->getStyle('A1:L1')->getFont()->getColor()->setARGB('FFFFFF');
            $sheet->getStyle('A1:L1')->getFill()->setFillType(Fill::FILL_SOLID)->getStartColor()->setARGB('ff6600');
            $sheet->setTitle("Catalogue des rapports BO");
            $sheet->setCellValue('A1', 'N°');
            $sheet->setCellValue('B1', 'Dossier');
            $sheet->setCellValue('C1', 'Sous-Dossier');
            $sheet->setCellValue('D1', 'Nom du rapport');
            $sheet->setCellValue('E1', 'Version actuelle');
            $sheet->setCellValue('F1', 'Commentaire');
            $sheet->setCellValue('G1', 'Catégorie');
            $sheet->setCellValue('H1', 'Objectifs');
            $sheet->setCellValue('I1', 'Détails');
            $sheet->setCellValue('J1', 'Sources');
            $sheet->setCellValue('K1', 'Paramètres');
            $sheet->setCellValue('L1', 'Historique des versions');
            $sheet->fromArray(
                $res,
                null,
                'A2'
            );
            $highestRow = $sheet->getHighestRow(); // e.g. 10
            $highestColumn = $sheet->getHighestColumn(); // e.g 'F'
            $highestColumnIndex = \PhpOffice\PhpSpreadsheet\Cell\Coordinate::columnIndexFromString($highestColumn); // e.g. 5
            for ($row = 1; $row <= $highestRow; ++$row) {
                for ($col = 1; $col <= $highestColumnIndex; ++$col) {
                    $value = $sheet->getCellByColumnAndRow($col, $row)->setValue(str_replace("<br>", "&char(10&", $sheet->getCellByColumnAndRow($col, $row)->getValue()));
                    $replaced = $value;
                }
            }


            // Create your Office 2007 Excel (XLSX Format)
            $writer = new Xlsx($spreadsheet);

            // e.g /var/www/project/public/my_first_excel_symfony4.xlsx
            $fileName = 'Catalogue des rapports BO.xlsx';
            $temp_file = tempnam(sys_get_temp_dir(), $fileName);
            // Create the file
            $writer->save($temp_file);

            // Return a text response to the browser saying that the excel was succesfully created
            return $this->file($temp_file, $fileName, ResponseHeaderBag::DISPOSITION_INLINE);
        }else{
            return $this->redirectToRoute('app_login');
        }
    }

    /**
     * @Route("/importation", name="Importation")
     *
     * @throws \PhpOffice\PhpSpreadsheet\Reader\Exception
     * @throws \PhpOffice\PhpSpreadsheet\Exception
     */
    public function importation(Request $request, ImportsRepository $importsRepository, ReportCatalogRepository $catalogRepository, DossierRepository $dossierRepository, SousDossierRepository $sousDossierRepository)
    {

        $em = $this->getDoctrine()->getManager();
        $import = new Imports();
        $form = $this->createForm(ImportType::class, $import);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $import->setLastDate(new \DateTime('now'));
            $excelFile = $import->getExcelFile();
            if ($excelFile) {
                $excelFileName = pathinfo($excelFile->getClientOriginalName(), PATHINFO_FILENAME);
                $newFileName = $excelFileName . '-' . $import->getLastDate()->format('d-m-Y') . '.' . $excelFile->guessExtension();

                try {
                    $excelFile->move(
                        $this->getParameter('excel_imports'),
                        $newFileName
                    );
                } catch (FileException $e) {
                    // ... handle exception if something happens during file upload
                }

                $import->setExcelFile($newFileName);

                $em->persist($import);
                $em->flush();


                $type = 'Xlsx';

                $reader = IOFactory::createReader($type);
                $spreadsheet = $reader->load($this->getParameter('excel_imports') . '/' . $import->getExcelFile());
                $activesheet = $spreadsheet->getActiveSheet()->toArray();
                foreach ($catalogRepository->findAll() as $report) {
                    $em->remove($report);
                    $em->flush();
                }
                foreach ($sousDossierRepository->findAll() as $sub) {
                    $em->remove($sub);
                    $em->flush();
                }
                foreach ($dossierRepository->findAll() as $folder) {
                    $em->remove($folder);
                    $em->flush();
                }
                unset($activesheet[0]);
                foreach ($activesheet as $line) {
                    $report = new ReportCatalog();


                        $report->setN((int)$line[0]);


                    if (!is_null($dossierRepository->findOneBy(['nomDossier' => $line[1]]))) {
                        //alors ajoute dans le rapport le nom du dossier (OBJET)
                        $report->setMainFolder($dossierRepository->findOneBy(['nomDossier' => $line[1]]));
                        //Si Le sous dossier existe
                        if (!is_null($sousDossierRepository->findOneBy(['nomDossier' => $line[2]]))) {
                            //alors insere le sous dossier dans le rapport (OBJET)
                            $report->setSubFolder($sousDossierRepository->findOneBy(['nomDossier' => $line[2]]));
                        } else if(!is_null($line[2])){
                            //Sinon créer un sous dossier
                            $sousDossier = new SousDossier();
                            $sousDossier->setNomDossier($line[2]);
                            //ajoute le dossier parent qui existe déjà
                            $sousDossier->setMainFolder($dossierRepository->findOneBy(['nomDossier' => $line[1]]));
                            $em->persist($sousDossier);
                            $em->flush();
                            //Ajoute le sous dossier dans le rapport (OBJET)
                            $report->setSubFolder($sousDossier);
                        }
                    } else {
                        //Sinon créer un dossier
                        $dossier = new Dossier();
                        if (!is_null($line[1])) {
                            $dossier->setNomDossier($line[1]);
                        }
                        $em->persist($dossier);
                        $em->flush();
                        //Ajouter le dossier dans le rapport (OBJET)
                        $report->setMainFolder($dossier);
                        //CONDITION SI SOUS DOSSIER EXISTE, SINON NOUVEAU SOUS DOSSIER
                        if (!is_null($sousDossierRepository->findOneBy(['nomDossier' => $line[2]]))) {
                            $report->setSubFolder($sousDossierRepository->findOneBy(['nomDossier' => $line[2]]));
                        } else if(!is_null($line[2])){
                            $sousDossier = new SousDossier();
                            $sousDossier->setNomDossier($line[2]);
                            $sousDossier->setMainFolder($dossier);
                            $em->persist($sousDossier);
                            $em->flush();
                            $report->setSubFolder($sousDossier);
                        }
                    }

                    // ------------                 CAS DES DOSSIERS ET SOUS-DOSSIERS         ---------------
                    if (!is_null($line[1])) {
                        $report->setMainFolder($dossierRepository->findOneBy(['nomDossier' => $line[1]]));
                    }
                    if (!is_null($line[2])) {
                        $report->setSubFolder($sousDossierRepository->findOneBy(['nomDossier' => $line[2]]));
                    }
                    //Si le dossier existe déjà


                    // ------------                FIN DU CAS DES DOSSIERS ET SOUS-DOSSIERS         ---------------

                    if(!is_null($line[3]))
                    {
                        $report->setNomRapport($line[3]);
                    }
                    if(!is_null($line[4]))
                    {
                        $report->setVersionActuelle($line[4]);
                    }
                    if(!is_null($line[5]))
                    {
                        $report->setCommentaire($line[5]);
                    }
                    if(!is_null($line[6]))
                    {
                        $report->setCategorie($line[6]);
                    }
                    if(!is_null($line[7]))
                    {
                        $report->setObjectifs($line[7]);
                    }
                    if(!is_null($line[8]))
                    {
                        $report->setDetails($line[8]);
                    }
                    if(!is_null($line[9]))
                    {
                        $report->setSources($line[9]);
                    }
                    if(!is_null($line[10]))
                    {
                        $report->setParametres($line[10]);
                    }
                    if(!is_null($line[11]))
                    {
                        $report->setHistoriqueVersions($line[11]);
                    }
                    $report->setCreatedBy($this->getUser());
                    $report->setCreationDate(new \DateTime('now'));
                    $report->setUpdateNb(0);
                    $report->setLastUpdate(new \DateTime('now'));
                    $em->persist($report);
                    $em->flush();


                }





                }




            /*
             * $this->addFlash('success',"L'importation s'est bien dérouléee.");
            return $this->redirectToRoute("Importation");
             */
        }
        return $this->render('admin/importation.html.twig', [
            'imports'=>$importsRepository->findAll(),
            'form'=>$form->createView()
        ]);
    }

    /**
     * @Route("/supprimer-importation/{id}", name="supprimer-importation")
     */
    public function deleteImport(Request $request)
    {
        if($request->isMethod('POST'))
        {

        }
        return $this->render('admin/profile.html.twig');
    }

}
