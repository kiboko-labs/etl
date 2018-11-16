<?php

namespace Kiboko\Component\ETL\Flow\Extractor;

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
     * @return \Generator
     */
    public function extract(): \Generator
    {
        yield from $this->data;
    }
}
