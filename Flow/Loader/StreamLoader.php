<?php

namespace Kiboko\Component\ETL\Flow\Loader;

use Kiboko\Component\ETL\Pipeline\Bucket\AcceptanceResultBucket;

abstract class StreamLoader implements LoaderInterface
{
    /**
     * @var resource
     */
    private $stream;

    /**
     * @param resource $stream
     */
    public function __construct($stream)
    {
        if (!is_resource($stream) || get_resource_type($stream) !== 'stream') {
            throw new \InvalidArgumentException(
                'Argument provided is not the valid type, please provide a stream resource.'
            );
        }

        $this->stream = $stream;
    }

    public function load(): \Generator
    {
        while (true) {
            $line = yield;

            fwrite($this->stream, $this->formatLine($line));

            yield new AcceptanceResultBucket($line);
        }
    }

    abstract protected function formatLine($line);
}
