<?php

namespace Kiboko\Component\ETL\Flow\Extractor;

use Box\Spout\Reader\CSV\Reader;
use Box\Spout\Reader\SheetInterface;

class SpoutCsvExtractor implements ExtractorInterface
{
    /**
     * @var Reader
     */
    private $reader;

    /**
     * @param Reader $reader
     */
    public function __construct(Reader $reader)
    {
        $this->reader = $reader;
    }

    /**
     * @return \Generator
     *
     * @throws \Box\Spout\Reader\Exception\ReaderNotOpenedException
     */
    public function extract(): \Generator
    {
        /** @var SheetInterface $sheet */
        foreach ($this->reader->getSheetIterator() as $sheet) {
            yield from $sheet->getRowIterator();
        }
    }
}
