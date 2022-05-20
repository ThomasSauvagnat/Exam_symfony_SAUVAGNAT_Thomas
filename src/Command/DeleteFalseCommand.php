<?php

namespace App\Command;

use App\Repository\ProductRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

#[AsCommand(
    name: 'app:delete-false',
    description: 'Supprime tous les produits qui sont
en isActive false de la base de donnée',
)]
class DeleteFalseCommand extends Command
{
    private ProductRepository $productRepository;
    private EntityManagerInterface $entityManager;

    public function __construct(ProductRepository $productRepository, EntityManagerInterface $entityManager)
    {
        parent::__construct();
        $this->productRepository = $productRepository;
        $this->entityManager = $entityManager;
    }

    protected function configure(): void
    {
        $this
            ->addArgument('arg1', InputArgument::OPTIONAL, 'Argument description')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $falseProducts = $this->productRepository->findBy(['isActive' => false]);
        foreach ($falseProducts as $falseProduct) {
            $this->entityManager->remove($falseProduct);
        }
        $this->entityManager->flush();
        $output->writeln('Tous les produits en isActive false ont été supprimés !');
        return Command::SUCCESS;
    }
}
