<?php

namespace Astina\Bundle\RedirectManagerBundle\Command;

use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
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
            )
            ->addOption(
                'redirect-code',
                'c',
                InputOption::VALUE_OPTIONAL,
                'Specify the HTTP Status code for imported urls.',
                302
            )
            ->addOption(
                'count-redirects',
                null,
                InputOption::VALUE_NONE,
                'Should redirects be counted?'
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
        $redirectCode   = $input->getOption('redirect-code');
        $countRedirects = $input->getOption('count-redirects');

        if (is_readable($file)) {
            $output->writeln(sprintf('<info>Importing file `%s` ...</info>', $file));

            /** @var CsvImporter $doctrine */
            $csvImporter = $this->getContainer()->get('armb.csv_importer');

            $count = $csvImporter->import($file, $redirectCode, $countRedirects);

            $output->writeln(sprintf('<info>Successfully imported %d url redirects.</info>', $count));

        } else {
            $output->writeln(sprintf('<error>File `%s` doesn\'t exists or is unreadable.</error>', $file));
        }
    }
}
