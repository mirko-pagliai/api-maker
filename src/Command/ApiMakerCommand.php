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
use ApiMaker\Command\ApiMakerCommandSubscriber;
use Exception;
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
        $target = $input->getOption('target') ?? add_slash_term(ROOT) . 'output';
        $options['title'] = $input->getOption('title') ?? null;
        $options['debug'] = $input->getOption('debug') ?? false;

        $output->writeln('Reading sources from: ' . $sources);
        $output->writeln('Target directory: ' . $target);

        $start = microtime(true);
        $apiMaker = new ApiMaker($sources, $options);
        $dispatcher = $apiMaker->getEventDispatcher();
        $dispatcher->addSubscriber(new ApiMakerCommandSubscriber($output));

        try {
            $apiMaker->build($target);
        } catch (Exception $e) {
            $output->writeln(sprintf('<error>Error: %s</error>', $e->getMessage()));

            return Command::FAILURE;
        }

        $output->writeln(sprintf('Elapsed time: %s seconds', round(microtime(true) - $start, 2)));

        return Command::SUCCESS;
    }
}
