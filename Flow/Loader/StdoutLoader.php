<?php

namespace Kiboko\Component\ETL\Flow\Loader;

use Kiboko\Component\ETL\Pipeline\GenericBucket;

class StdoutLoader implements LoaderInterface
{
    public function load(): \Generator
    {
        while (true) {
            $line = yield;

            file_put_contents('php://stdout', var_export($line, true) . PHP_EOL);
            yield new GenericBucket($line);
        }
    }
}
