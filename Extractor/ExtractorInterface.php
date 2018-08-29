<?php

namespace Kiboko\Component\ETL\Extractor;


interface ExtractorInterface
{
    /**
     * Extract data from the given source.
     *
     * @return \Iterator
     */
    public function extract(): \Iterator;
}
