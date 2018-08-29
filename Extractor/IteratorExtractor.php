<?php

namespace Kiboko\Component\ETL\Extractor;

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
     * @return \Iterator
     */
    public function extract(): \Iterator
    {
        yield from $this->traversable;
    }
}
