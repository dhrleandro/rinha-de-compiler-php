<?php

namespace LeandroDaher\RinhaDeCompilerPhp\Nodes;

class Location
{
    public int $start;
    public int $end;
    public string $filename;

    public function __construct(int $start = -1, int $end = -1, string $filename = '')
    {
        $this->start = $start;
        $this->end = $end;
        $this->filename = $filename;
    }

    public static function create(int $start = -1, int $end = -1, string $filename = ''): Location
    {
        return new Location($start, $end, $filename);
    }
}
