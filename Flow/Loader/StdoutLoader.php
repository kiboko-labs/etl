<?php

namespace Kiboko\Component\ETL\Flow\Loader;

class StdoutLoader extends StreamLoader
{
    public function __construct()
    {
        parent::__construct(STDOUT);
    }

    protected function formatLine($line)
    {
        return var_export($line, true) . PHP_EOL;
    }
}
