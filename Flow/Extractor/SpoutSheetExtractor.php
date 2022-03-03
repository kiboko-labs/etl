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
     * @var int
     */
    private $skipLines;

    /**
     * @param SheetInterface $sheet
     * @param int $skipLines
     */
    public function __construct(
        SheetInterface $sheet,
        int $skipLines = 0
    ) {
        $this->sheet = $sheet;
        $this->skipLines = $skipLines;
    }

    public function extract(): \Generator
    {
        $iterator = $this->sheet->getRowIterator();
        $iterator->rewind();

        $this->skipLines($iterator, $this->skipLines);

        $columns = $iterator->current()->toArray();
        $columnCount = count($columns);

        $iterator->next();

        $currentLine = $this->skipLines + 1;
        while ($iterator->valid()) {
            $line = $iterator->current()->toArray();
            $cellCount = count($line);

            if ($columnCount < $cellCount) {
                throw new \RuntimeException(strtr(
                    'The columns and cell counts read from the line %line% does not match. Expected %expected% cells, got %actual%.',
                    [
                        '%line%' => $currentLine,
                        '%expected%' => $columnCount,
                        '%actual%' => $cellCount,
                    ]
                ));
            } else if ($columnCount > $cellCount) {
                $line = array_pad($line, $columnCount, null);
            }

            yield array_combine($columns, $line);

            $iterator->next();
            $currentLine++;
        }
    }

    private function skipLines(\Iterator $iterator, int $skipLines)
    {
        for ($i = 0; $i < $skipLines; $i++) {
            $iterator->next();

            if (!$iterator->valid()) {
                throw new \RuntimeException('Reached unexpected end of source.');
            }
        }
    }
}
