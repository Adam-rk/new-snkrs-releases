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
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Email;

#[AsCommand(
    name: 'scrapper:start',
    description: 'Add a short description for your command',
)]
class ScrapperStartCommand extends Command
{
    public function __construct(
        private GetPageContent $getPageContent,
        private MailerInterface $mailer
    ) {
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
        $path = getcwd() . '/src/previousData.txt';

        $pageContent = $this->getPageContent->getPageContent($uri);

        $fileContent = file_get_contents($path);

        $fileContent = eval("return $fileContent;");

        if ($pageContent === $fileContent) {
            $io->note('Nothing has changed');
            return Command::SUCCESS;
        } else {
            $fileSystem = new Filesystem();

            $body = implode(PHP_EOL, $pageContent);

            $email = (new Email())
                ->from('scrapper@adamrafik.com')
                ->to('adam.rafik@ecole-hexagone.com')
                ->subject('The website has changed!')
                ->text("New articles: \n" . $body . "\n The website: " . $uri);

            $this->mailer->send($email);

            $fileSystem->dumpFile($path, var_export($pageContent, true));

            $io->success('Email sent successfully!');
            return Command::SUCCESS;
        }
    }
}
