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
use Symfony\Component\Console\Style\SymfonyStyle;

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
            ->addOption('target', 't', InputOption::VALUE_REQUIRED, 'Target directory');
    }

    /**
     * Exebutes the command
     * @param \Symfony\Component\Console\Input\InputInterface $input InputInterface instance
     * @param \Symfony\Component\Console\Input\OutputInterface $output OutputInterface instance
     * @return int
     */
    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);

        $sources = $input->getArgument('sources');
        $target = $input->getOption('target') ?? add_slash_term(ROOT) . 'output';
        $options['title'] = $input->getOption('title') ?? null;
        $options['debug'] = $input->getOption('debug') ?? false;

        $io->text('Reading sources from: ' . $sources);
        $io->text('Target directory: ' . $target);
        $io->text('=================================');

        $start = microtime(true);

        try {
            $apiMaker = new ApiMaker($sources, $options);
            $apiMaker->getEventDispatcher()->addSubscriber(new ApiMakerCommandSubscriber($io));
            $apiMaker->build($target);
        } catch (Exception $e) {
            $io->error($e->getMessage());

            return Command::FAILURE;
        }

        $io->text(sprintf('Elapsed time: %s seconds', round(microtime(true) - $start, 2)));
        $io->success('Done!');

        return Command::SUCCESS;
    }
}
