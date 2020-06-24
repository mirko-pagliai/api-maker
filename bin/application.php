#!/usr/bin/env php
<?php
require dirname(__DIR__) . '/vendor/autoload.php';

use PhpDocMaker\Command\ApiMakerCommand;
use Symfony\Component\Console\Application;

$command = new ApiMakerCommand();
$application = new Application();
$application->add($command);
$application->setDefaultCommand($command->getName());
$application->run();
