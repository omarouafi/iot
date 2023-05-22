<?php

namespace App\Command;

use App\Entity\Module;
use App\Entity\Fonctionement;
use App\Entity\Mesure;
use App\Repository\FonctionementRepository;
use App\Repository\MesureRepository;
use App\Repository\ModuleRepository;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class GenerateModuleDataCommand extends Command
{
    protected static $defaultName = 'generate-module-data';
    protected static $defaultDescription = 'Generates modules, status, and measurements data';

    protected ModuleRepository $moduleRepository; 
    protected MesureRepository $mesureRepository; 
    protected FonctionementRepository $fonctionementRepository; 

    public function __construct(
        ModuleRepository $moduleRepository,
        MesureRepository $mesureRepository,
        FonctionementRepository $fonctionementRepository
    ) {
        $this->moduleRepository = $moduleRepository;
        $this->mesureRepository = $mesureRepository;
        $this->fonctionementRepository = $fonctionementRepository;

        parent::__construct();
    }

    protected function configure(): void
    {
        $this->setDescription(self::$defaultDescription);
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        // Générer les données des modules
        $moduleNames = ['Module 1', 'Module 2', 'Module 3', 'Module 4', 'Module 5'];
        $moduleDescriptions = [
            'Description 1',
            'Description 2',
            'Description 3',
            'Description 4',
            'Description 5'
        ];
        $moduleMesureTypes = ['Température', 'Vitesse'];

        foreach ($moduleNames as $index => $moduleName) {
            // Créer un nouveau module
            $module = new Module();
            $module->setName($moduleName);
            $module->setDescription($moduleDescriptions[$index]);
            $module->setMesureType($moduleMesureTypes[array_rand($moduleMesureTypes)]);

            $this->moduleRepository->save($module, true);

            // Générer un statut aléatoire pour le module
            $status = ['OK', 'Failure'];
            $randomStatus = $status[array_rand($status)];

            // Créer des entrées de fonctionnement pour le module
            for ($i = 0; $i < 5; $i++) {
                $fonctionement = new Fonctionement();
                $fonctionement->setModule($module);
                $fonctionement->setStatus($randomStatus);
                $fonctionement->setCreatedAt($this->generateRandomDateTime());
                $this->fonctionementRepository->save($fonctionement, true);
            }

            // Simuler des mesures de données pour le module
            for ($i = 0; $i < 10; $i++) {
                $mesure = new Mesure();
                $mesure->setModule($module);
                $mesure->setValue(rand(1, 100));
                $mesure->setCreatedAt($this->generateRandomDateTime());

                $this->mesureRepository->save($mesure, true);
            }

            $output->writeln('Module ' . $moduleName . ' créé avec succès.');
        }

        return Command::SUCCESS;
    }

    private function generateRandomDateTime(): \DateTime
    {
        $startDate = strtotime('-1 year');
        $endDate = time();

        $randomTimestamp = mt_rand($startDate, $endDate);

        return new \DateTime("@$randomTimestamp");
    }
}
