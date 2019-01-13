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
        if ($this->file->eof()) {
            return;
        }

        while (!$this->file->eof()) {
            yield json_decode($this->file->fgets(), JSON_OBJECT_AS_ARRAY);
        }
    }
}
