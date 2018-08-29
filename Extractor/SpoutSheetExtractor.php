<?php

namespace Kiboko\Component\ETL\Extractor;

use Box\Spout\Reader\SheetInterface;

class SpoutSheetExtractor implements ExtractorInterface
{
    /**
     * @var SheetInterface
     */
    private $sheet;

    /**
     * @param SheetInterface $sheet
     */
    public function __construct(SheetInterface $sheet)
    {
        $this->sheet = $sheet;
    }

    /**
     * @return \Iterator
     */
    public function extract(): \Iterator
    {
        yield from $this->sheet;
    }
}
