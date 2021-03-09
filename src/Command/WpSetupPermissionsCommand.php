<?php

namespace Djvue\WpAdminBundle\Command;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\Filesystem\Exception\IOExceptionInterface;
use Symfony\Component\Filesystem\Filesystem;

class WpSetupPermissionsCommand extends Command
{
    private Filesystem $filesystem;
    protected static $defaultName = 'wp:setup-permissions';

    public function __construct(Filesystem $filesystem)
    {
        parent::__construct();
        $this->filesystem = $filesystem;
    }

    protected function configure()
    {
        $this
            ->setDescription('Setup directories permissions')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);

        try {
            $this->setupDirectoriesPermissions();
        } catch (IOExceptionInterface $exception) {
            $io->error($exception->getMessage());
        }

        $io->success('Directory permissions setup success!');

        return Command::SUCCESS;
    }

    /**
     * @throws IOExceptionInterface
     */
    private function setupDirectoriesPermissions(): void
    {
        $this->filesystem->mkdir('var/wp-uploads', 777);
        $this->filesystem->chmod([
            'var/wp-uploads',
            'var/log',
            'var/cache'
        ], 777);
    }
}
