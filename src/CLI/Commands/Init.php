<?php

namespace Windsor\CLI\Commands;

use Windsor\CLI\Command;
use Windsor\CLI\CreateSchema;

class Init extends Command
{
    protected function handle(array $args, array $params, array $flags): bool
    {
        $dir = $this->projectDir;
        if(array_key_exists("dir", $params)) $dir = $params['dir'];

        $cwd = dirname(__FILE__);

        $projectDir = preg_replace("/(([\\\\\/])(Dev)([\\\\\/])).*/", "", $cwd);
        $binDir = $projectDir . $this->separator() . "bin";
        $examplesDir = $projectDir . $this->separator() . "Examples";

        $windsorSource = $binDir . $this->separator() . "windsor";
        $windsorTarget = $dir . $this->separator() . "windsor";

        $envSource = $examplesDir . $this->separator() . ".env.example";
        $envTarget = $dir . $this->separator() . ".env.Windsor.example";

        copy($windsorSource, $windsorTarget);
        copy($envSource, $envTarget);

        return true;
    }

    protected function schema() {

        if(1) { //if env is set or db connection was successful
            CreateSchema::create();
            $this->success("Windsor schema created successfully!", true);
        }

    }

}