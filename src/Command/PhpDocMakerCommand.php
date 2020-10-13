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

use ErrorException;
use Exception;
use PhpDocMaker\Command\PhpDocMakerCommandSubscriber;
use PhpDocMaker\PhpDocMaker;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\Console\Terminal;

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
    protected static $defaultName = 'make';

    /**
     * Sets the configuration
     * @return void
     */
    protected function configure(): void
    {
        $this
            ->addArgument('source', InputArgument::OPTIONAL, 'Path from which to read the sources. If not specified, the current directory will be used', getcwd())
            ->addOption('debug', null, InputOption::VALUE_NONE, 'Enables debug. This automatically activates verbose mode and disables the cache')
            ->addOption('no-cache', null, InputOption::VALUE_NONE, 'Disables the cache')
            ->addOption('target', 't', InputOption::VALUE_REQUIRED, 'Target directory where to generate the documentation. If not specified, the `output` directory will be created', getcwd() . DS . 'output')
            ->addOption('title', null, InputOption::VALUE_REQUIRED, 'Title of the project. If not specified, the title will be self-determined using the name of the source directory');
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
        //Reads and parses configuration from xml file
        $xmlConfigFile = add_slash_term($input->getArgument('source')) . 'php-doc-maker.xml';
        if (is_readable($xmlConfigFile)) {
            $SimpleXMLElement = simplexml_load_string(file_get_contents($xmlConfigFile));
            $options = json_decode(json_encode($SimpleXMLElement), true);

            if ($options['verbose'] ?? false) {
                $output->setVerbosity(OutputInterface::VERBOSITY_VERBOSE);
                unset($options['verbose']);
            }

            foreach ($options as $name => $value) {
                $option = $this->getDefinition()->getOption($name);

//                if (!$option->getDefault() && $value === 'true') {
//                    $input->setOption($name, true);
//                } else
                {
                    $value = $value === 'true' ? true : ($value === 'false' ? false : $value);
                    $input->setOption($name, $value);
//                    $option->setDefault($value);
                }
            }
        }

        //Debug mode enables verbose mode and disables cache
        if ($input->getOption('debug')) {
            $output->setVerbosity(OutputInterface::VERBOSITY_VERBOSE);
            $input->setOption('no-cache', true);
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
        $newLine = str_repeat('=', (new Terminal())->getWidth());
        [$source, $target] = [$input->getArgument('source'), $input->getOption('target')];
        $start = microtime(true);

        $io->text('Sources directory: ' . $source);
        $io->text('Target directory: ' . $target);
        $output->writeln($newLine);

        if ($output->isVerbose()) {
            $io->text('Verbose mode enabled');

            if ($input->getOption('no-cache')) {
                $io->text('The cache has been disabled');
            }
            if ($input->getOption('debug')) {
                $io->text('Debug mode enabled');
            }

            $output->writeln($newLine);
        }

        //Parses options
        $options = [];
        if ($input->getOption('no-cache')) {
            $options['cache'] = false;
        }
        foreach (['debug', 'title'] as $name) {
            if ($input->getOption($name)) {
                $options[$name] = $input->getOption($name);
            }
        }

        //Turns all PHP errors into exceptions
        set_error_handler(function ($severity, $message, $filename, $lineno) {
            //Error was suppressed with the @-operator
            if (0 === error_reporting()) {
                return false;
            }

            throw new ErrorException($message, 0, $severity, $filename, $lineno);
        });

        try {
            $this->PhpDocMaker = $this->PhpDocMaker ?? new PhpDocMaker($source, $target, $options);
            $this->PhpDocMaker->getEventDispatcher()->addSubscriber(new PhpDocMakerCommandSubscriber($io));
            $this->PhpDocMaker->build();
        } catch (Exception $e) {
            $message = $e->getMessage() . '...' . PHP_EOL . sprintf('On file `%s`, line %s', $e->getFile(), $e->getLine());
            if ($input->getOption('debug')) {
                $message .= PHP_EOL . PHP_EOL . $e->getTraceAsString();
            }

            $io->error($message);

            return defined('Command::FAILURE') ? Command::FAILURE : 1;
        } finally {
            restore_error_handler();
        }

        $io->text(sprintf('Elapsed time: %s seconds', round(microtime(true) - $start, 2)));
        $io->success('Done!');

        return defined('Command::SUCCESS') ? Command::SUCCESS : 0;
    }
}
