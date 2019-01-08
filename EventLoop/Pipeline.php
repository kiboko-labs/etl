<?php

namespace Kiboko\Component\ETL\EventLoop;

use Kiboko\Component\ETL\Flow\Extractor\ExtractorInterface;

class Pipeline
{
    /**
     * @param ExtractorInterface $extractor
     *
     * @return \Generator
     */
    public function extract(ExtractorInterface $extractor): \Generator
    {
        $iterator = $extractor->extract();
        $iterator->rewind();

        while ($iterator->valid()) {
            $line = yield $iterator->current();
            $iterator->next();
        }
    }
}
