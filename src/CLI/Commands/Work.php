<?php

namespace Windsor\CLI\Commands;

use Windsor\CLI\Traits\ExecutesSystemCommand;

class Work extends \Windsor\CLI\Command
{
    use ExecutesSystemCommand;

    protected function handle(array $args, array $params, array $flags): bool
    {
        $exec = realpath(".") . "/vendor/bin/psysh";
        $this->stream("php " . $exec);
        return true;
    }
}