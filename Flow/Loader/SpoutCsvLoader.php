<?php

namespace Kiboko\Component\ETL\Flow\Loader;

use Box\Spout\Writer\CSV\Writer;

class SpoutCsvLoader implements LoaderInterface
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
        while ($line = yield) {
            $this->writer->addRow($line);
            yield $line;
        }
    }
}
