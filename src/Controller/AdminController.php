<?php

namespace App\Controller;


use App\Entity\Dossier;
use App\Entity\Imports;
use App\Entity\ReportCatalog;
use App\Entity\SousDossier;
use App\Form\EditPasswordType;
use App\Form\ImportType;
use App\Form\UserEditType;
use App\Form\UserNotGrantedEditType;
use App\Repository\DossierRepository;
use App\Repository\ImportsRepository;
use App\Repository\ReportCatalogRepository;
use App\Repository\SousDossierRepository;
use Doctrine\ORM\EntityManagerInterface;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\ResponseHeaderBag;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Authentication\Token\UsernamePasswordToken;
use Symfony\Component\Security\Core\Authorization\AccessDecisionManagerInterface;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use Symfony\Component\Security\Core\User\UserInterface;



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
     * @Route("/profil/{id}", name="Modifier-Profil")
     */
    public function editProfile(AccessDecisionManagerInterface $accessDecisionManager, Request $request, UserPasswordEncoderInterface $encoder, UserInterface $user, EntityManagerInterface $entityManager)
    {
        $currentPw = $user->getPassword();

        $token = new UsernamePasswordToken($user, 'none', 'none', $user->getRoles());
        if (!$accessDecisionManager->decide($token, ['ROLE_SUPER_ADMIN'])) {
            $form = $this->createForm(UserNotGrantedEditType::class, $user);
            $form->handleRequest($request);
            if ($form->isSubmitted() && $form->isValid()) {
                    $user->setPassword($currentPw);
                    $this->getDoctrine()->getManager()->flush();
                    return $this->redirectToRoute('app_logout');

            }
        }else{
            $form = $this->createForm(UserEditType::class, $user);
            $form->handleRequest($request);
            if ($form->isSubmitted() && $form->isValid()) {
                    $user->setPassword($currentPw);
                    $this->getDoctrine()->getManager()->flush();
                    return $this->redirectToRoute('app_logout');
            }

        };




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
            if($user->getResponseSecrete() == $form->get('reponsesecrete')->getData()) {
                if ($encoder->isPasswordValid($user, $form->get('oldPassword')->getData())) {
                    $newencodedPassword = $encoder->encodePassword($user, $user->getPlainPassword());
                    $user->setPassword($newencodedPassword);
                    $this->getDoctrine()->getManager()->flush();
                    return $this->redirectToRoute('app_logout');
                } else {
                    $this->addFlash('err', "L'ancien mot de passe ne correspond pas");
                    return $this->redirectToRoute('Modifier-mot-de-passe');
                }
            }else{
                $this->addFlash('err', "La réponse secrète ne correspond pas");
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
            $conn = $em->getConnection();
            $platform   = $conn->getDatabasePlatform();
            $conn->executeQuery('SET FOREIGN_KEY_CHECKS = 0;');
            $truncateSql1 = $platform->getTruncateTableSQL('report_catalog');
            $truncateSql2 = $platform->getTruncateTableSQL('dossier');
            $truncateSql3 = $platform->getTruncateTableSQL('sous_dossier');
            $truncateSql4 = $platform->getTruncateTableSQL('ref_obj_rapport');
            $truncateSql5 = $platform->getTruncateTableSQL('referentiel_objets');
            $truncateSql6 = $platform->getTruncateTableSQL('matrice');
            $truncateSql7 = $platform->getTruncateTableSQL('liensaxes_objets');
            $truncateSql8 = $platform->getTruncateTableSQL('liens');
            $conn->executeUpdate($truncateSql1);
            $conn->executeUpdate($truncateSql2);
            $conn->executeUpdate($truncateSql3);
            $conn->executeUpdate($truncateSql4);
            $conn->executeUpdate($truncateSql5);
            $conn->executeUpdate($truncateSql6);
            $conn->executeUpdate($truncateSql7);
            $conn->executeUpdate($truncateSql8);
            $conn->executeQuery('SET FOREIGN_KEY_CHECKS = 1;');
            $import->setLastDate(new \DateTime('now'));
            $excelFile = $import->getExcelFile();
            if ($excelFile) {
                $excelFileName = pathinfo($excelFile->getClientOriginalName(), PATHINFO_FILENAME);
                $newFileName = $excelFileName . '-' . $import->getLastDate()->format('dmYhis') . '.' . $excelFile->guessExtension();

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
                $fileType = 'Xlsx';

                $reader = IOFactory::createReader($fileType);

                $sp = $reader->load($this->getParameter('excel_imports').'/'.$import->getExcelFile());

                $arr = $sp->getActiveSheet()->toArray();
                unset($arr[0]);
                for($i = 1;$i<count($arr);$i++)
                {
                    $report = new ReportCatalog();

                    $report->setN($arr[$i][0]);
                    $report->setNomRapport($arr[$i][3]);
                    $report->setLastUpdate(new \DateTime('now'));
                    $report->setCreatedBy($this->getUser());
                    $report->setCreationDate(new \DateTime('now'));
                    $report->setUpdateNb(0);
                    if(!is_null($arr[$i][4]))
                    {
                        $report->setVersionActuelle($arr[$i][4]);
                    }
                    if(!is_null($arr[$i][5]))
                    {
                        $report->setCommentaire($arr[$i][5]);
                    }
                    if(!is_null($arr[$i][6]))
                    {
                        $report->setCategorie($arr[$i][6]);
                    }
                    if(!is_null($arr[$i][7]))
                    {
                        $report->setObjectifs($arr[$i][7]);
                    }
                    if(!is_null($arr[$i][8]))
                    {
                        $report->setDetails($arr[$i][8]);
                    }
                    if(!is_null($arr[$i][9]))
                    {
                        $report->setSources($arr[$i][9]);
                    }
                    if(!is_null($arr[$i][10]))
                    {
                        $report->setParametres($arr[$i][10]);
                    }
                    if(!is_null($arr[$i][11]))
                    {
                        $report->setHistoriqueVersions($arr[$i][11]);
                    }
                    if($dossierRepository->findOneBy(['nomDossier'=>$arr[$i][1]]))
                    {
                        $mainf = $dossierRepository->findOneBy(['nomDossier'=>$arr[$i][1]]);
                        $report->setMainFolder($mainf);

                        if(!is_null($arr[$i][2]))
                        {
                            if($sousDossierRepository->findOneBy(['nomDossier'=>$arr[$i][2]]))
                            {
                                $subf = $sousDossierRepository->findOneBy(['nomDossier'=>$arr[$i][2]]);
                                $report->setSubFolder($subf);
                            }else{
                                $subf = new SousDossier();
                                $subf->setNomDossier($arr[$i][2]);
                                $subf->setMainFolder($mainf);
                                $em->persist($subf);
                                $em->flush();

                                $report->setSubFolder($subf);
                            }
                        }
                    }else{
                        $mainf = new Dossier();
                        $mainf->setNomDossier($arr[$i][1]);
                        $em->persist($mainf);
                        $em->flush();
                        $report->setMainFolder($mainf);

                        if(!is_null($arr[$i][2]))
                        {
                            if($sousDossierRepository->findOneBy(['nomDossier'=>$arr[$i][2]]))
                            {
                                $subf = $sousDossierRepository->findOneBy(['nomDossier'=>$arr[$i][2]]);
                                $report->setSubFolder($subf);
                            }else{
                                $subf = new SousDossier();
                                $subf->setNomDossier($arr[$i][2]);
                                $subf->setMainFolder($mainf);
                                $em->persist($subf);
                                $em->flush();

                                $report->setSubFolder($subf);
                            }
                        }
                    }


                    $em->persist($report);
                    $em->flush();
                }
                $this->addFlash('success',"L'importation s'est bien dérouléee.");
                return $this->redirectToRoute("Importation");
            }
        }
        return $this->render('admin/importation.html.twig', [
            'imports'=>$importsRepository->findAll(),
            'form'=>$form->createView()
        ]);
    }

    /**
     * @Route("/supprimer-importation/{id}", name="supprimer-importation", methods={"DELETE"})
     */
    public function deleteImport(Request $request, Imports $imports)
    {
        if ($this->isCsrfTokenValid('delete'.$imports->getId(), $request->request->get('_token'))) {
            $entityManager = $this->getDoctrine()->getManager();
            $fs = new FileSystem();
            $fs->remove($this->getParameter('excel_imports').'/'.$imports->getExcelFile());
            $entityManager->remove($imports);
            $entityManager->flush();
        }

        return $this->redirectToRoute('Importation');
    }


    /**
     * @Route("/recherche-rapport", name="recherche-rapport", methods={"POST"})
     */
    public function searchReport(Request $request, ReportCatalogRepository $catalogRepository)
    {
        if($request->isMethod("POST"))
        {
            $query = $catalogRepository->createQueryBuilder('a')
                ->select('a.Nom_Rapport','a.id')
                ->where('a.Nom_Rapport LIKE :rapport')
                ->setParameter('rapport','%'.$request->get('t').'%')
                ->getQuery();

            $result = $query->getArrayResult();
            return new JsonResponse($result);
        }
    }
}
