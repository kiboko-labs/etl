<?php

namespace Kiboko\Component\ETL\Extractor;

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
     * @return \Iterator
     *
     * @throws \Box\Spout\Reader\Exception\ReaderNotOpenedException
     */
    public function extract(): \Iterator
    {
        /** @var SheetInterface $sheet */
        foreach ($this->reader->getSheetIterator() as $sheet) {
            yield from $sheet->getRowIterator();
        }
    }
}
