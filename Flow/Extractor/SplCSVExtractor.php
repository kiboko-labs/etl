<?php

namespace Kiboko\Component\ETL\Flow\Extractor;

class SplCSVExtractor implements ExtractorInterface
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

    /**
     * @return \Generator
     */
    public function extract(): \Generator
    {
        $this->cleanBom();
        
        if ($this->file->eof()) {
            return;
        }

        $columns = $this->file->fgetcsv($this->delimiter, $this->enclosure, $this->escape);

        $columnsCount = count($columns);

        $lineNumber = 0;
        while (!$this->file->eof()) {
            $line = $this->file->fgetcsv($this->delimiter, $this->enclosure, $this->escape);
            $lineColumnsCount = count($line);
            ++$lineNumber;

            if ($lineColumnsCount !== $columnsCount) {
                continue;
                throw new \RuntimeException(strtr(
                    'The line %line% does not contain the same amount of columns than the first line.',
                    [
                        '%line%' => $lineNumber
                    ]
                ));
            }

            yield array_combine($columns, $line);
        }
    }

    public function cleanBom()
    {
        $bom = $this->file-> fread(3);
        if (!preg_match('/^\\xEF\\xBB\\xBF$/', $bom)) {
            $this->file-> seek(0);
        }
    }
}
