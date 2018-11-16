<?php

namespace Kiboko\Component\ETL\Flow\Extractor;

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
     * @return \Generator
     */
    public function extract(): \Generator
    {
        yield from $this->sheet;
    }
}
