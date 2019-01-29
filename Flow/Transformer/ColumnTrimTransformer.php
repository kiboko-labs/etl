<?php

namespace Kiboko\Component\ETL\Flow\Transformer;

use Kiboko\Component\ETL\Pipeline\GenericBucket;

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
        while (true) {
            $line = yield;

            foreach ($this->columnsToTrim as $column) {
                if (!isset($line[$column])) {
                    continue;
                }

                $line[$column] = trim($line[$column]);
            }

            yield new GenericBucket($line);
        }
    }
}
