<?php
declare(strict_types=1);

/**
 * This file is part of api-maker.
 *
 * Licensed under The MIT License
 * For full copyright and license information, please see the LICENSE.txt
 * Redistributions of files must retain the above copyright notice.
 *
 * @copyright   Copyright (c) Mirko Pagliai
 * @link        https://github.com/mirko-pagliai/api-maker
 * @license     https://opensource.org/licenses/mit-license.php MIT License
 */
namespace ApiMaker\Command;

use ApiMaker\ApiMaker;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * ApiMakerCommand
 */
class ApiMakerCommand extends Command
{
    /**
     * Name of the command
     * @var string
     */
    protected static $defaultName = 'apimaker';

    /**
     * Sets the configuration
     * @return void
     */
    protected function configure(): void
    {
        $this
            ->setDescription('Creates a new user.')
            ->addArgument('sources', InputArgument::REQUIRED, 'Path or paths from which to read the sources')
            ->addOption('debug', null, InputOption::VALUE_NONE, 'Enables debug')
            ->addOption('title', null, InputOption::VALUE_REQUIRED, 'Title of the project')
            ->addOption('target', 't', InputOption::VALUE_REQUIRED, 'Target directory')
        ;
    }

    /**
     * Exebutes the command
     * @param \Symfony\Component\Console\Input\InputInterface $input InputInterface instance
     * @param \Symfony\Component\Console\Input\OutputInterface $output OutputInterface instance
     * @return int
     */
    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $sources = $input->getArgument('sources');
        $target = $input->getOption('target') ?? ROOT . DS . 'output';

        $output->writeln('Reading sources from ' . $sources);

        $options = [];
        if ($input->getOption('title')) {
            $options['title'] = $input->getOption('title');
        }
        if ($input->getOption('debug')) {
            $options['debug'] = $input->getOption('debug');
        }

        $apiMaker = new ApiMaker($sources, $options);
        $apiMaker->build($target);

        return Command::SUCCESS;
    }
}
