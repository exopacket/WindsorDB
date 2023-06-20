<?php

namespace Windsor\CLI\Traits;

use Windsor\CLI\Traits\DetectsOS;

trait ChecksForExistence
{

    use DetectsOS;

    protected function serviceInstalled(string $service): bool
    {
        return true;
    }

    protected function executableExists(string $binary): bool
    {
        return true;
    }

    protected function packageInstalled(string $package): bool
    {
        return true;
    }

    protected function fileExists(string $file): bool
    {
        return true;
    }

    protected function inFile(string $file, string $needle): bool
    {
        return true;
    }

}