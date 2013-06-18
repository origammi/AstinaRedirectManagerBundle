<?php

namespace Astina\Bundle\RedirectManagerBundle\Command;

use Astina\Bundle\RedirectManagerBundle\Service\CsvImporter;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Bridge\Doctrine\RegistryInterface;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Class ImportUrlsCommand
 *
 * @package   Astina\Bundle\RedirectManagerBundle\Command
 * @author    Matej Velikonja <mvelikonja@astina.ch>
 * @copyright 2013 Astina AG (http://astina.ch)
 */
class ImportUrlsCommand extends ContainerAwareCommand
{
    /**
     * Configures the command.
     */
    protected function configure()
    {
        $this
            ->setName('armb:import')
            ->setDescription('Import URLs from CSV file to Astina Redirect Manager bundle.')
            ->addArgument(
                'file',
                InputArgument::REQUIRED,
                'Path of file to import.'
            );
    }

    /**
     * @param InputInterface  $input
     * @param OutputInterface $output
     *
     * @return int
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $file = $input->getArgument('file');

        if (is_readable($file)) {
            $output->writeln(sprintf('<info>Importing file `%s` ...</info>', $file));

            /** @var RegistryInterface $doctrine */
            $doctrine = $this->getContainer()->get('doctrine');

            $csvImporter = new CsvImporter($doctrine);
            $count = $csvImporter->import($file);

            $output->writeln(sprintf('<info>Successfully imported %d url redirects.</info>', $count));

        } else {
            $output->writeln(sprintf('<error>File `%s` doesn\'t exists or is unreadable.</error>', $file));
        }
    }
}