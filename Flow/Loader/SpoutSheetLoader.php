<?php

namespace Kiboko\Component\ETL\Flow\Loader;

use Box\Spout\Writer\WriterInterface;

class SpoutSheetLoader implements LoaderInterface
{
    /**
     * @var WriterInterface
     */
    private $sheet;

    /**
     * @param WriterInterface $sheet
     */
    public function __construct(WriterInterface $sheet)
    {
        $this->sheet = $sheet;
    }

    public function load(): \Generator
    {
        while ($line = yield) {
            $this->sheet->addRow($line);
            yield $line;
        }
    }
}
