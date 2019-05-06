<?php

namespace Kiboko\Component\ETL\Flow\Loader;

use Kiboko\Component\ETL\Pipeline\Bucket\AcceptanceResultBucket;

class LDJSONLoader implements LoaderInterface
{
    /**
     * @var \SplFileObject
     */
    private $file;

    /**
     * @param \SplFileObject $file
     */
    public function __construct(\SplFileObject $file)
    {
        $this->file = $file;
    }

    public function load(): \Generator
    {
        while (true) {
            $line = yield;

            $this->file->fwrite(json_encode($line) . "\n");

            yield new AcceptanceResultBucket($line);
        }
    }
}
