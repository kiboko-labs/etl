<?php

namespace Kiboko\Component\ETL\Flow\Loader;

use Box\Spout\Writer\CSV\Writer;
use Kiboko\Component\ETL\Pipeline\Bucket\AcceptanceResultBucket;

class SpoutLoader implements LoaderInterface
{
    /**
     * @var Writer
     */
    private $writer;

    /**
     * @param Writer $writer
     */
    public function __construct(Writer $writer)
    {
        $this->writer = $writer;
    }

    public function load(): \Generator
    {
        $isFirstLine = true;
        while (true) {
            $line = yield;

            if ($isFirstLine === true) {
                $this->writer->addRow(array_keys($line));
                $isFirstLine = false;
            }

            $this->writer->addRow($line);

            yield new AcceptanceResultBucket($line);
        }
    }
}
