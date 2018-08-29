<?php

namespace Kiboko\Component\ETL\Transformer;

class ColumnTrimTransformer implements TransformerInterface
{
    /**
     * @var array
     */
    private $columnsToTrim;

    /**
     * @param array $columnsToTrim
     */
    public function __construct(array $columnsToTrim)
    {
        $this->columnsToTrim = $columnsToTrim;
    }

    public function transform(): \Generator
    {
        while ($line = yield) {
            foreach ($this->columnsToTrim as $column) {
                if (!isset($line[$column])) {
                    continue;
                }

                $line[$column] = trim($line[$column]);
            }

            yield $line;
        }
    }
}
