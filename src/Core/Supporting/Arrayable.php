<?php

namespace Windsor\Core\Supporting;

interface Arrayable
{
    public function get(string $path, string $cast = null);
    public function json(string $path = null) : string;
    public function xml(string $path = null) : string;
    public function toArray() : array;
    public function exists(string $path) : bool;
    public function first(string $path);
    public function isArray(string $path) : bool;
    public function keys() : array;
    public function values() : array;
}