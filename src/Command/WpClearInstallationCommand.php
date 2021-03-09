<?php

namespace Djvue\WpAdminBundle\Command;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\Filesystem\Exception\IOExceptionInterface;
use Symfony\Component\Filesystem\Filesystem;

class WpClearInstallationCommand extends Command
{
    private Filesystem $filesystem;
    protected static $defaultName = 'wp:clear-installation';

    public function __construct(Filesystem $filesystem)
    {
        parent::__construct();
        $this->filesystem = $filesystem;
    }

    protected function configure()
    {
        $this
            ->setDescription('Clear Wordpress unused installation files')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);
        $io->success('Execution started!');

        try {
            $this->rmFilesAndDirectories();
        } catch (IOExceptionInterface $exception) {
            $io->error($exception->getMessage());
        }

        $io->success('Wordpress unused installation files cleared!');

        return Command::SUCCESS;
    }

    /**
     * @throws IOExceptionInterface
     */
    private function rmFilesAndDirectories(): void
    {
        $this->filesystem->remove([
            'public/wp/wp-content',
            'public/wp/composer.json',
            'public/wp/license.txt',
            'public/wp/readme.html',
            'public/wp/wp-config-sample.php'
        ]);
    }
}
