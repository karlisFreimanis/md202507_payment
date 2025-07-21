<?php

namespace App\Command;

use App\Repository\PaymentRepository;
use DateTimeImmutable;
use DateTimeInterface;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

#[AsCommand(
    name: 'ShowPaymentsByDate',
    description: 'Shows payments for a given date',
)]
class ShowPaymentsByDateCommand extends Command
{
    public function __construct(
        private readonly PaymentRepository $paymentRepository,
    ) {
        parent::__construct();
    }

    protected function configure(): void
    {
        $this
            ->addArgument('report', InputArgument::REQUIRED, 'Report type (e.g., "report")')
            ->addOption('date', null, InputOption::VALUE_REQUIRED, 'Date for filtering payments (YYYY-MM-DD)');
    }

    /**
     * @throws \DateMalformedStringException
     */
    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io              = new SymfonyStyle($input, $output);
        $periodStartDate = $this->validateDate($io, $input);
        if (!isset($periodStartDate)) {
            return Command::FAILURE;
        }
        $periodEndDate = $periodStartDate->modify('+1 day');

        $this->drawHeader(
            $io,
            $periodStartDate,
            $periodEndDate
        );

        $payments = $this->paymentRepository->findByPaymentDatePeriod(
            $periodStartDate,
            $periodEndDate,
        );

        if (empty($payments)) {
            $io->warning('No payments found for that date.');
            return Command::SUCCESS;
        }

        $this->drawTable($io, $payments);

        return Command::SUCCESS;
    }

    private function drawTable(
        SymfonyStyle $io,
        array $payments
    ): void {
        $headers = array_keys($payments[0]);
        $rows    = [];

        foreach ($payments as $payment) {
            $rows[] = array_map(function ($value) {
                if ($value instanceof \DateTimeInterface) {
                    return $value->format(DateTimeInterface::ATOM);
                }
                return (string)$value;
            }, $payment);
        }

        $io->table($headers, $rows);
    }

    private function drawHeader(
        SymfonyStyle $io,
        DateTimeImmutable $periodStartDate,
        DateTimeImmutable $periodEndDate
    ): void {
        $io->writeln(
            sprintf(
                "Period start: %s\nPeriod end:   %s",
                $periodStartDate->format(DateTimeInterface::ATOM),
                $periodEndDate->format(DateTimeInterface::ATOM),
            )
        );
    }

    private function validateDate(
        SymfonyStyle $io,
        InputInterface $input,
    ): ?DateTimeImmutable {
        $dateInput = $input->getOption('date');

        if (!$dateInput) {
            $io->error('You must provide a date using --date=YYYY-MM-DD');
            return null;
        }

        try {
            return new DateTimeImmutable($dateInput);
        } catch (\Exception $e) {
            $io->error('Invalid date format. Use YYYY-MM-DD.');
            return null;
        }
    }

}
