<?php

namespace Kiboko\Component\ETL\Extractor;

class ArrayExtractor implements ExtractorInterface
{
    /**
     * @var array
     */
    private $data;

    /**
     * @param array $data
     */
    public function __construct(array $data)
    {
        $this->data = $data;
    }

    /**
     * @return \Iterator
     */
    public function extract(): \Iterator
    {
        yield from $this->data;
    }
}
