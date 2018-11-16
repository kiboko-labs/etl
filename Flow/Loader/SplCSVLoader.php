<?php

namespace Kiboko\Component\ETL\Flow\Loader;

class SplCSVLoader implements LoaderInterface
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

    public function load(): \Generator
    {
        while (!$this->file->eof()) {
            $line = yield $this->file->fgetcsv();
        }
    }
}
