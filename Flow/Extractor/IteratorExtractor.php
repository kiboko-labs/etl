<?php

namespace Kiboko\Component\ETL\Flow\Extractor;

class IteratorExtractor implements ExtractorInterface
{
    /**
     * @var array
     */
    private $traversable;

    /**
     * @param \Traversable $traversable
     */
    public function __construct(\Traversable $traversable)
    {
        $this->traversable = $traversable;
    }

    /**
     * @return \Generator
     */
    public function extract(): \Generator
    {
        yield from $this->traversable;
    }
}
