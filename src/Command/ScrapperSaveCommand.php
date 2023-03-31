<?php

namespace App\Command;

use App\Service\GetPageContent;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\Filesystem\Filesystem;

#[AsCommand(
    name: 'scrapper:save',
    description: 'Add a short description for your command',
)]
class ScrapperSaveCommand extends Command
{
    public function __construct(
        private GetPageContent $getPageContent
    )
    {
        parent::__construct();
    }
    protected function configure(): void
    {
        $this
            ->addArgument('uri', InputArgument::REQUIRED, 'The URI of the website');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);
        $uri = $input->getArgument('uri');

        $content = $this->getPageContent->getPageContent($uri);
    
        $path = getcwd() . '/src/previousData.txt';
        $fileSystem = new Filesystem();

        if (!$fileSystem->exists($path)) {
            $fileSystem->touch($path);
        }

        $fileSystem->dumpFile($path, var_export($content, true));

        $io->success('Data has been put here: ' . $path);

        return Command::SUCCESS;
    }
}
