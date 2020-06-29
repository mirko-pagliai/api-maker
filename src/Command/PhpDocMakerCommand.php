<?php
declare(strict_types=1);

/**
 * This file is part of php-doc-maker.
 *
 * Licensed under The MIT License
 * For full copyright and license information, please see the LICENSE.txt
 * Redistributions of files must retain the above copyright notice.
 *
 * @copyright   Copyright (c) Mirko Pagliai
 * @link        https://github.com/mirko-pagliai/php-doc-maker
 * @license     https://opensource.org/licenses/mit-license.php MIT License
 */
namespace PhpDocMaker\Command;

use Exception;
use PhpDocMaker\Command\PhpDocMakerCommandSubscriber;
use PhpDocMaker\PhpDocMaker;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

/**
 * PhpDocMakerCommand
 */
class PhpDocMakerCommand extends Command
{
    /**
     * @var \PhpDocMaker\PhpDocMaker
     */
    public $PhpDocMaker;

    /**
     * Name of the command
     * @var string
     */
    protected static $defaultName = 'php-doc-maker';

    /**
     * Sets the configuration
     * @return void
     */
    protected function configure(): void
    {
        $this
            ->addArgument('source', InputArgument::REQUIRED, 'Path from which to read the sources')
            ->addOption('debug', null, InputOption::VALUE_NONE, 'Enables debug')
            ->addOption('title', null, InputOption::VALUE_REQUIRED, 'Title of the project')
            ->addOption('target', 't', InputOption::VALUE_REQUIRED, 'Target directory');
    }

    /**
     * Initializes the command after the input has been bound and before the input
     *  is validated
     * @param \Symfony\Component\Console\Input\InputInterface $input InputInterface instance
     * @param \Symfony\Component\Console\Input\OutputInterface $output OutputInterface instance
     * @return void
     */
    protected function initialize(InputInterface $input, OutputInterface $output): void
    {
        //Reads configuration from xml file
        $xmlConfigFile = add_slash_term($input->getArgument('source')) . 'php-doc-maker.xml';
        if (is_readable($xmlConfigFile)) {
            $SimpleXMLElement = simplexml_load_string(file_get_contents($xmlConfigFile));
            $options = json_decode(json_encode($SimpleXMLElement), true);

            foreach ($options as $name => $value) {
                if (!$input->getOption($name)) {
                    $value = $value === 'true' ? true : ($value === 'false' ? false : $value);
                    $input->setOption($name, $value);
                }
            }
        }
    }

    /**
     * Executes the command
     * @param \Symfony\Component\Console\Input\InputInterface $input InputInterface instance
     * @param \Symfony\Component\Console\Input\OutputInterface $output OutputInterface instance
     * @return int
     */
    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);

        $source = $input->getArgument('source');
        $target = $input->getOption('target') ?? add_slash_term(ROOT) . 'output';

        $io->text('Reading sources from: ' . $source);
        $io->text('Target directory: ' . $target);
        $io->text('===================================================');

        $start = microtime(true);

        try {
            if (!$this->PhpDocMaker) {
                $options = [];
                foreach (['debug', 'title'] as $name) {
                    if ($input->getOption($name)) {
                        $options[$name] = $input->getOption($name);
                    }
                }

                $this->PhpDocMaker = new PhpDocMaker($source, $options);
            }

            $this->PhpDocMaker->getEventDispatcher()->addSubscriber(new PhpDocMakerCommandSubscriber($io));
            $this->PhpDocMaker->build($target);
        } catch (Exception $e) {
            $message = $e->getMessage() . PHP_EOL . sprintf('On file `%s`, line %s', $e->getFile(), $e->getLine());
            if ($input->getOption('debug')) {
                $message .= PHP_EOL . PHP_EOL . $e->getTraceAsString();
            }

            $io->error($message);

            return Command::FAILURE;
        }

        $io->text(sprintf('Elapsed time: %s seconds', round(microtime(true) - $start, 2)));
        $io->success('Done!');

        return Command::SUCCESS;
    }
}
