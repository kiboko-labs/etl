<?php

namespace Kiboko\Component\ETL\Flow\Extractor;

class LDJSONExtractor implements ExtractorInterface
{
    /**
     * @var \SplFileObject
     */
    private $file;

    /**
     * @param \SplFileObject $file
     */
    public function __construct(\SplFileObject $file)
    {
        $this->file = $file;
    }

    /**
     * @return \Generator
     */
    public function extract(): \Generator
    {
        while (!$this->file->eof()) {
            yield json_decode($this->file->fgets(), true);
        }
    }
}
