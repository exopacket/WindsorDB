<?php

namespace Windsor\CLI\Commands;

use Windsor\CLI\Command;
use Windsor\CLI\CreateSchema;

class Hook extends Command
{
    protected function handle(array $args, array $params, array $flags): bool
    {
        $dir = $this->projectDir;
        if(!array_key_exists("id", $params)) $this->error("--id is required.", true, 1);

        return true;
    }

}