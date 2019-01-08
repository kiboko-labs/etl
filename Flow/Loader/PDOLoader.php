<?php

namespace Kiboko\Component\ETL\Flow\Loader;

class PDOLoader implements LoaderInterface
{
    /**
     * @var \PDOStatement
     */
    private $preparedStatement;

    /**
     * @var callable
     */
    private $fieldMapping;

    public function __construct(\PDOStatement $preparedStatement, callable $fieldMapping = null)
    {
        $this->preparedStatement = $preparedStatement;
        $this->fieldMapping = $fieldMapping ?? function($line) {
            return $line;
        };
    }

    public function load(): \Generator
    {
        while ($line = yield) {
            if ($this->preparedStatement->execute(($this->fieldMapping)($line))) {
                yield $line;
            }
        }
    }
}
