<?php

namespace Kiboko\Component\ETL\Flow\Extractor;


interface ExtractorInterface
{
    /**
     * Extract data from the given source.
     *
     * @return \Generator
     */
    public function extract(): \Generator;
}
