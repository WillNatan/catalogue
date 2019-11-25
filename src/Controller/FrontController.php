<?php

namespace App\Controller;

use App\Entity\Dossier;
use App\Entity\ReportCatalog;
use App\Repository\ReportCatalogRepository;
use Doctrine\ORM\EntityManagerInterface;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Reader\Xlsx as ExcelReader;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Style\Style;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\ResponseHeaderBag;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\User\User;

class FrontController extends AbstractController
{
    /**
     * @Route("/", name="Main")
     */
    public function index(ReportCatalogRepository $catalogRepository)
    {
        return $this->redirectToRoute('app_login');
    }


}
