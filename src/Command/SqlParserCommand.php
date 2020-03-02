<?php

namespace App\Command;

use App\Entity\ReferentielObjets;
use App\Entity\RefObjRapport;
use App\Entity\Reports;
use App\Repository\ReportCatalogRepository;
use Psr\Container\ContainerInterface;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

class SqlParserCommand extends Command
{
    protected static $defaultName = 'sql-parser';

    private $container;

    public function __construct(ContainerInterface $container)
    {
        parent::__construct();
        $this->container = $container;
    }


    protected function configure()
    {
        $this
            ->setDescription('Permet de parser une requête SQL à 3 rangs. EX : UTILISATEUR.TABLE.CHAMP')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);

        /*Entity Manager*/
        $em = $this->container->get('doctrine');


        /* Pour tous les rapports qui existent dans la base de donnée */
        foreach ($em->getRepository(Reports::class)->findAll() as $report)
        {
            /* Pour toutes les références objets-rapports qui existent dans la base de donnée qui ont pour ID le rapport de la boucle courante */
            foreach($em->getRepository(RefObjRapport::class)->findBy(['rapport'=>$report->getNomRapport()]) as $ref)
            {
                /* Supprime toutes les entrées dans la référence (PURGE REF) */
                $em->getManager()->remove($ref);
                $em->flush();
            }
            /* Pattern pour le RegExp */
            $pattern='/[a-zA-Z_0-9]+\.+[a-zA-Z_0-9]+\.+[a-zA-Z_0-9]+/';
            /* Récupère toutes les lignes qui ont pour RegExp SCHEMA.TYPE.CHAMP depuis le champ SQLText de la table des rapports */
            preg_match_all($pattern, $report->getSqltext(), $match);


            /* Containte d'unicité entre les objets récupérés dans le SQL */
            $unique = array_unique($match[0]);
            /* Pour tous les objets récupérés */
            foreach ($unique as $m)
            {
                /* Si l'objet récupéré existe déjà dans la base de données */
                if($em->getRepository(ReferentielObjets::class)->findOneBy(['nomObjet'=>$m]))
                {
                    /* Alors récupérer ce dernier et commencer à modifier ses champs */
                    $objet = $em->getRepository(ReferentielObjets::class)->findOneBy(['nomObjet'=>$m]);
                    /* S'il y a une référence ayant pour nom de rapport celui de la boucle actuelle (Ligne 46) Alors mofifier également */
                    if($em->getRepository(RefObjRapport::class)->findOneBy(['rapport'=>$report, 'objet'=>$objet])){
                        /* Découpage en 3 parties de l'objet */
                        $split = explode('.', $m, 3);
                        $objet->setNomObjet(strtoupper($m));
                        $objet->setSchemaObj(strtoupper($split[0]));
                        $objet->setTableobj(strtoupper($split[1]));
                        $objet->setChamp(strtoupper($split[2]));
                        $em->getManager()->flush();
                    }else{
                        $split = explode('.', $m, 3);
                        $objet->setNomObjet(strtoupper($m));
                        $objet->setSchemaObj(strtoupper($split[0]));
                        $objet->setTableobj(strtoupper($split[1]));
                        $objet->setChamp(strtoupper($split[2]));
                        $referentiel = new RefObjRapport();
                        $referentiel->setObjet($objet);
                        $referentiel->setRapport($report);
                        $referentiel->setNomObjet(strtoupper($m));
                        $referentiel->setNomRapport($report->getNomRapport());
                        $em->getManager()->persist($referentiel);
                        $em->getManager()->flush();
                    }

                }
                else{
                    $objet = new ReferentielObjets();
                    $objet->setNomObjet(strtoupper($m));
                    $split = explode('.', $m, 3);
                    $objet->setSchemaObj(strtoupper($split[0]));
                    $objet->setTableobj(strtoupper($split[1]));
                    $objet->setChamp(strtoupper($split[2]));
                    $referentiel = new RefObjRapport();
                    $referentiel->setObjet($objet);
                    $referentiel->setRapport($report);
                    $referentiel->setNomObjet(strtoupper($m));
                    $referentiel->setNomRapport($report->getNomRapport());
                    $em->getManager()->persist($referentiel);
                    $em->getManager()->persist($objet);
                    $em->getManager()->flush();
                }
            }
        }

        $io->success('Commande éxécutée');

        return 0;
    }
}
