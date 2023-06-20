<?php

namespace Windsor\CLI\Traits;

use Windsor\CLI\Types\OS;

trait DetectsOS
{

    protected function os(): OS
    {
        if(strotupper(substr(php_uname("s"), 0, 3)) === "WIN") return OS::WINDOWS;
        if(strtoupper(substr(php_uname("s"), 0, 5)) === "LINUX") return OS::DEBIAN;
        return OS::WINDOWS;
    }

    protected function useBash(): bool
    {
        return $this->os() === OS::DEBIAN || $this->os() === OS::FEDORA || $this->os() === OS::MACOS;
    }

    protected function useCmd(): bool
    {
        return $this->os() === OS::WINDOWS;
    }

}