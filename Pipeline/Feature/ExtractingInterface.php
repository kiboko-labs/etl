<?php

namespace Kiboko\Component\ETL\Pipeline\Feature;

use Kiboko\Component\ETL\Extractor\ExtractorInterface;

interface ExtractingInterface
{
    /**
     * @param ExtractorInterface $extractor
     *
     * @return $this
     */
    public function extract(ExtractorInterface $extractor): ExtractingInterface;
}
