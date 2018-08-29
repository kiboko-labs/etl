<?php

namespace Kiboko\Component\ETL\Loader;

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
        while ($line = yield) {
            $this->file->fputcsv($line);
            yield $line;
        }
    }
}
