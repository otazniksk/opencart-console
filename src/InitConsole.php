<?php

namespace otazniksk\OpencartConsole;

use otazniksk\OpencartConsole\Command\CreateExtensionCommand;
use Symfony\Component\Console\Application;

class InitConsole
{
    public function __construct($dir)
    {
        try {
            $application = new Application();

            // Commands
            $application->add(new CreateExtensionCommand($dir));

            // Run Console App
            return $application->run();

        } catch (\Exception $e) {
            echo 'Caught exception: ',  $e->getMessage(), "\n";
        }
    }
}