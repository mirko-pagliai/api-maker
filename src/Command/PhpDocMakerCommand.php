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
            ->addArgument('source', InputArgument::OPTIONAL, 'Path from which to read the sources. If not specified, the current directory will be used')
            ->addOption('debug', null, InputOption::VALUE_NONE, 'Enables debug')
            ->addOption('no-cache', null, InputOption::VALUE_NONE, 'Disables cache')
            ->addOption('target', 't', InputOption::VALUE_REQUIRED, 'Target directory where to generate the documentation. If not specified, the `output` directory will be created')
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
        if (!$input->getArgument('source')) {
            $input->setArgument('source', getcwd());
        }

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

        if (!$input->getOption('target')) {
            $input->setOption('target', add_slash_term($input->getArgument('source')) . 'output');
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
        [$source, $target] = [$input->getArgument('source'), $input->getOption('target')];

        $io->text('Sources directory: ' . $source);
        $io->text('Target directory: ' . $target);
        $output->writeln(str_repeat('=', (new Terminal())->getWidth()));

        $start = microtime(true);

        try {
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

            $this->PhpDocMaker = $this->PhpDocMaker ?: new PhpDocMaker($source, $options);
            $this->PhpDocMaker->getEventDispatcher()->addSubscriber(new PhpDocMakerCommandSubscriber($io));
            $this->PhpDocMaker->build($target);
        } catch (Exception $e) {
            $message = $e->getMessage() . PHP_EOL . sprintf('On file `%s`, line %s', $e->getFile(), $e->getLine());
            if ($input->getOption('debug')) {
                $message .= PHP_EOL . PHP_EOL . $e->getTraceAsString();
            }

            $io->error($message);

            return defined('Command::FAILURE') ? Command::FAILURE : 1;
        }

        $io->text(sprintf('Elapsed time: %s seconds', round(microtime(true) - $start, 2)));
        $io->success('Done!');

        return defined('Command::SUCCESS') ? Command::SUCCESS : 0;
    }
}
