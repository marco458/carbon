<?php

namespace App\Command;

use App\Constant\SupportedFqcn;
use App\Entity\Factor\FactorUser;
use Carbon\Carbon;
use Core\Repository\UserRepository;
use Core\Service\FileReader\CsvFileReaderService;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

class ImportTestDatasetCommand extends Command
{
    public function __construct(
        private CsvFileReaderService $csvFileReader,
        private UserRepository $userRepository,
        string $name = null
    ) {
        parent::__construct($name);
    }

    protected function configure()
    {
        $this
            ->setName('import:test-dataset')
            ->setDescription('Import test data dataset')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);

        $filePath = 'assets/import/EnergyConsumption.csv';
        if (file_exists($filePath)) {
            [$file] = (array) fopen($filePath, 'r');
        } else {
            $io->error('File not found');

            return Command::FAILURE;
        }

        $user = $this->userRepository->findBy(['email' => 'admin@admin.com']);

        $header = fgetcsv($file);
        while (($row = fgetcsv($file)) !== false) {
            $data = array_combine($header, $row);
            $io->writeln($data);

            /*
                        $factorUser = new FactorUser();
                        $factorUser->setUser($user);
                        $factorUser->setFactorId((int) $element['factor_id']);
                        $factorUser->setFactorFqcn(SupportedFqcn::mapClassNameToFqcn($element['sector']));

                        $factorUser->setAmount((float) $element['JedMj']);
                        $factorUser->setDate(Carbon::create($element['Datum']));
                        $factorUser->setUnit('');
            */
        }

        return Command::SUCCESS;
    }
}
