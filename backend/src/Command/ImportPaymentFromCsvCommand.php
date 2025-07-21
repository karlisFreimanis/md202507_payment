<?php

namespace App\Command;

use App\Service\Csv\PaymentImportCsvService;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

#[AsCommand(
    name: 'ImportPaymentFromCsv',
    description: 'Imports payments from a CSV file',
)]
class ImportPaymentFromCsvCommand extends Command
{
    public function __construct(
        private readonly PaymentImportCsvService $paymentImportCsvService
    ) {
        parent::__construct();
    }

    protected function configure(): void
    {
        $this
            ->addArgument('import', InputArgument::REQUIRED, 'Action to perform (must be "import")')
            ->addOption('file', null, InputOption::VALUE_REQUIRED, 'Path to the CSV file');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);

        if (!$this->isValidAction($input->getArgument('import'))) {
            $io->error('Invalid command usage. Example: php bin/console ImportPaymentFromCsv import --file=/path/to/file.csv');
            return Command::FAILURE;
        }

        try {
            $filePath = $this->resolveAndValidateFilePath($input->getOption('file'));
        } catch (\RuntimeException $e) {
            $io->error($e->getMessage());
            return Command::FAILURE;
        }
        $report = [];
        $this->processCsvLineByLine($filePath, function(array $row) use (&$report): void {
            $code = $this->paymentImportCsvService->process($row);
            if(!isset($report[$code])) {
                $report[$code] = 1;
            } else {
                $report[$code] += 1;
            }
        });
        $this->paymentImportCsvService->getReportMessage($io, $report);
        return Command::SUCCESS;
    }

    private function isValidAction(?string $action): bool
    {
        return $action === 'import';
    }

    private function resolveAndValidateFilePath(?string $fileOption): string
    {
        if (!$fileOption) {
            throw new \RuntimeException('You must provide a file path using --file=...');
        }

        if (!$this->hasCsvExtension($fileOption)) {
            throw new \RuntimeException('The file must have a .csv extension.');
        }

        if (file_exists($fileOption)) {
            return realpath($fileOption);
        }

        $relativePath = __DIR__ . '/../../Resources/files/' . ltrim($fileOption, '/');

        if (file_exists($relativePath)) {
            return realpath($relativePath);
        }

        throw new \RuntimeException("File not found: $fileOption or fallback $relativePath");
    }

    private function hasCsvExtension(string $fileName): bool
    {
        return strtolower(pathinfo($fileName, PATHINFO_EXTENSION)) === 'csv';
    }

    /**
     * @throws \RuntimeException If the file cannot be opened
     */
    private function processCsvLineByLine(
        string $filePath,
        callable $processRow,
        string $delimiter = ',',
        string $enclosure = '"',
        string $escape = '\\'
    ): void {
        if (!is_readable($filePath)) {
            throw new \RuntimeException("CSV file is not readable: $filePath");
        }

        $handle = fopen($filePath, 'r');
        if ($handle === false) {
            throw new \RuntimeException("Failed to open CSV file: $filePath");
        }

        try {
            $headers = fgetcsv($handle, 0, $delimiter, $enclosure, $escape);
            if ($headers === false) {
                throw new \RuntimeException("CSV file is empty or header row could not be read: $filePath");
            }

            while (($row = fgetcsv($handle, 0, $delimiter, $enclosure, $escape)) !== false) {
                // Map row to headers (associative array)
                $assocRow = array_combine($headers, $row);
                if ($assocRow === false) {
                    throw new \RuntimeException('CSV row does not match header count.');
                }
                $processRow($assocRow);
            }
        } finally {
            fclose($handle);
        }
    }
}