<?php

namespace App\Controller;

use App\Entity\Dossier;
use App\Entity\ReportCatalog;
use App\Entity\SousDossier;
use App\Entity\User;
use App\Form\adminEditType;
use App\Form\EditPasswordType;
use App\Form\UserEditType;
use App\Repository\DossierRepository;
use App\Repository\ReportCatalogRepository;
use App\Repository\SousDossierRepository;
use Doctrine\ORM\EntityManagerInterface;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
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
    public function index(ReportCatalogRepository $catalogRepository, EntityManagerInterface $em)
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
            ->leftJoin('r.user', 'p')
            ->addSelect('p')
            ->select('COUNT(r) AS nombreRapport','p.username')
            ->groupBy('p.username')
            ->getQuery()
            ->getArrayResult();

        $qbRecette = $em->getRepository(ReportCatalog::class)->createQueryBuilder('r')
            ->leftJoin('r.mainFolder','p')
            ->addSelect('p')
            ->select('r.id','r.Nom_Rapport','p.nomDossier','r.VersionActuelle')
            ->where('p.nomDossier = :folder')
            ->setParameter('folder','RECETTE')
            ->groupBy('r.id','r.Nom_Rapport','p.nomDossier','r.VersionActuelle');
        $recette = $qbRecette->getQuery()->getArrayResult();

        $qbProd = $em->getRepository(ReportCatalog::class)->createQueryBuilder('r')
            ->leftJoin('r.mainFolder','p')
            ->addSelect('p')
            ->select('r.id','r.Nom_Rapport','p.nomDossier','r.VersionActuelle')
            ->where('p.nomDossier != :folder')
            ->setParameter('folder','RECETTE')
            ->groupBy('r.id','r.Nom_Rapport','p.nomDossier','r.VersionActuelle');

        $prod = $qbProd->getQuery()->getArrayResult();
        foreach($allReports as $report){
            $nbUpdate = $nbUpdate + $report->getUpdateNb();
        }

        return $this->render('admin/index.html.twig',['nbRapport'=>$nbRapport, 'nbUpdate'=>$nbUpdate, 'lastReport'=>$lastReport, 'production'=>$prod, 'recette'=>$recette, 'reportsByFolder'=>$qbReports, 'reportByUser'=>$qbReportsByUser]);
    }

    /**
     * @Route("/profil", name="Profil")
     */
    public function profile()
    {
        $user = $this->getUser();
        return $this->render('admin/profile.html.twig', ['user'=>$user]);
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
     * @Route("/modifier-mot-de-passe/{id}", name="Modifier-mot-de-passe")
     */
    public function editPassword(Request $request, UserPasswordEncoderInterface $encoder, User $user)
    {
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
            }
        }
        return $this->render('admin/editPassword.html.twig', [
            'user' => $user,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @throws \PhpOffice\PhpSpreadsheet\Exception
     * @Route(name="importReport", path="/import")
     */
    public function import(DossierRepository $dossierRepository, ReportCatalogRepository $catalogRepository, SousDossierRepository $sousDossier){
        $publicDirectory = $this->getParameter('kernel.project_dir') . '/public';
        $file = $publicDirectory.'/DWH_Catalogue_Rapports_BO.xlsx';
        $fileType = "Xlsx";
        $sheet = 'Détail';
        $reader = IOFactory::createReader($fileType);

        $reader->setLoadSheetsOnly($sheet);

        $spreadsheet = $reader->load($file);

        $sheetToArray = $spreadsheet->getActiveSheet()->toArray();
        $em = $this->getDoctrine()->getManager();
        unset($sheetToArray[0]);

        foreach ($sheetToArray as $data){
            $creport = $catalogRepository->findOneBy(['Nom_Rapport'=>$data[3]]);
            $report = new ReportCatalog();
            $report->setN($data[0]);
            $dossier = $dossierRepository->findOneBy(['nomDossier'=>$data[1]]);
            if(is_null($dossier) && !is_null($data[1])){
                $nd = new Dossier();
                $nd->setNomDossier($data[1]);
                $em->persist($nd);
                $em->flush();
            }
            $ss = $sousDossier->findOneBy(['nomDossier'=>$data[2]]);
            if(is_null($ss) && !is_null($data[2])){
                $nsd = new SousDossier();
                $nsd->setNomDossier($data[2]);
                $nsd->setMainFolder($dossier);
                $em->persist($nsd);
                $em->flush();
            }
            if(is_null($creport)){
                $report->setMainFolder($dossier);
                if(!is_null($ss)){
                    $report->setSubFolder($ss);
                }else{
                    $report->setSubFolder(null);
                }
                $report->setUser($this->getUser());
                if(!is_null($data[3])){
                    $report->setNomRapport($data[3]);
                }else{
                    $report->setNomRapport('Aucun nom de rapport');
                }

                if(!is_null($data[4])){
                    $report->setVersionActuelle($data[4]);
                }
                else{
                    $report->setVersionActuelle('1.O');
                }

                if(!is_null($data[5])){
                    $report->setCommentaire($data[5]);
                }
                else{
                    $report->setCommentaire('');
                }

                if(!is_null($data[6])){
                    $report->setCategorie($data[6]);
                }
                else{
                    $report->setCategorie('');
                }

                if(!is_null($data[7])){
                    $report->setObjectifs($data[7]);
                }
                else{
                    $report->setObjectifs('');
                }
                if(!is_null($data[8])){
                    $report->setDetails($data[8]);
                }
                else{
                    $report->setDetails('');
                }

                if(!is_null($data[9])){
                    $report->setSources($data[9]);
                }
                else{
                    $report->setSources('');
                }

                if(!is_null($data[10])){
                    $report->setParametres($data[10]);
                }
                else{
                    $report->setParametres('');
                }


                if(!is_null($data[11])){
                    $report->setHistoriqueVersions($data[11]);
                }
                else{
                    $report->setHistoriqueVersions('');
                }


                $report->setLastUpdate(new \DateTime('now'));
                $report->setUpdateNb($report->getUpdateNb()+1);
                $em->persist($report);
                $em->flush();
            }else{
                $creport->setMainFolder($dossier);
                if(!is_null($ss)){
                    $creport->setSubFolder($ss);
                }else{
                    $creport->setSubFolder(null);
                }
                $creport->setUser($this->getUser());
                $creport->setNomRapport($data[3]);

                if(!is_null($data[4])){
                    $creport->setVersionActuelle($data[4]);
                }
                else{
                    $creport->setVersionActuelle('1.O');
                }

                if(!is_null($data[5])){
                    $creport->setCommentaire($data[5]);
                }
                else{
                    $creport->setCommentaire('');
                }

                if(!is_null($data[6])){
                    $creport->setCategorie($data[6]);
                }
                else{
                    $creport->setCategorie('');
                }

                if(!is_null($data[7])){
                    $creport->setObjectifs($data[7]);
                }
                else{
                    $creport->setObjectifs('');
                }
                if(!is_null($data[8])){
                    $creport->setDetails($data[8]);
                }
                else{
                    $creport->setDetails('');
                }

                if(!is_null($data[9])){
                    $creport->setSources($data[9]);
                }
                else{
                    $creport->setSources('');
                }

                if(!is_null($data[10])){
                    $creport->setParametres($data[10]);
                }
                else{
                    $creport->setParametres('');
                }


                if(!is_null($data[11])){
                    $creport->setHistoriqueVersions($data[11]);
                }
                else{
                    $creport->setHistoriqueVersions('');
                }


                $creport->setLastUpdate(new \DateTime('now'));
                $creport->setUpdateNb($creport->getUpdateNb()+1);
                $em->flush();
            }
        }
        return $this->redirectToRoute('Administration');
    }


/*
 * $report = new ReportCatalog();
$report->setN($data[0]);
$dossier = $dossierRepository->findOneBy(['nomDossier'=>$data[1]]);
if($dossier){
$report->setMainFolder($dossier);
$sousdossier = $sousDossier->findOneBy(['nomDossier'=>$data[2]]);
if(!is_null($data[2])){
if($sousDossier){
$report->setSubFolder($sousdossier);
}else{
    $nsd = new SousDossier();
    $nsd->setNomDossier($data[2]);
    $nsd->setMainFolder($dossier);
    $em->persist($nsd);
    $em->flush();
    $report->setSubFolder($nsd);
}
}
}else{
    $nd = new Dossier();
    if(!is_null($data[1])){
        $nd->setNomDossier($data[1]);
        $em->persist($nd);
        $em->flush();
        $report->setMainFolder($nd);
        $sousdossier = $sousDossier->findOneBy(['nomDossier'=>$data[2]]);
        if(!is_null($data[2])){
            if($sousDossier){
                $report->setSubFolder($sousdossier);
            }else{
                $nsd = new SousDossier();
                $nsd->setMainFolder($dossier);
                $nsd->setNomDossier($data[2]);
                $em->persist($nsd);
                $em->flush();
                $report->setSubFolder($nsd);
            }
        }
    }
}
 */

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

}
