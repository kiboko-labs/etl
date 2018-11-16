<?php

namespace Kiboko\Component\ETL\Flow\Loader;

class StdoutLoader implements LoaderInterface
{
    public function load(): \Generator
    {
        while ($line = yield) {
            file_put_contents('php://stdout', var_export($line, true) . PHP_EOL);
            yield $line;
        }
    }
}
