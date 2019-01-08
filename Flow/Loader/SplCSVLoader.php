<?php

namespace Kiboko\Component\ETL\Flow\Loader;

class SplCSVLoader implements LoaderInterface
{
    /**
     * @var \SplFileObject
     */
    private $file;

    /**
     * @var string
     */
    private $delimiter;

    /**
     * @var string
     */
    private $enclosure;

    /**
     * @var string
     */
    private $escape;

    /**
     * @param \SplFileObject $file
     * @param string         $delimiter
     * @param string         $enclosure
     * @param string         $escape
     */
    public function __construct(
        \SplFileObject $file,
        string $delimiter = ',',
        string $enclosure = '"',
        string $escape = '\\'
    ) {
        $this->file = $file;
        $this->delimiter = $delimiter;
        $this->enclosure = $enclosure;
        $this->escape = $escape;
    }

    public function load(): \Generator
    {
        $isFirstLine = true;
        while ($line = yield) {
            if ($isFirstLine === true) {
                $this->file->fputcsv(array_keys($line), $this->delimiter, $this->enclosure, $this->escape);
                $isFirstLine = false;
            }

            $this->file->fputcsv($line, $this->delimiter, $this->enclosure, $this->escape);

            yield $line;
        }
    }
}
