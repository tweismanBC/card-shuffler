#!/usr/bin/env php
<?php

require __DIR__.'/vendor/autoload.php';

use Symfony\Component\Console\Application;
use App\Command\DealCardsCommand;

$application = new Application();

// ... register commands here:
$application->add(new DealCardsCommand());

$application->run();
