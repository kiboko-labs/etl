<?php

namespace Kiboko\Component\ETL\Flow\Loader;

use Kiboko\Component\ETL\Pipeline\EmptyBucket;
use Kiboko\Component\ETL\Pipeline\GenericBucket;

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
        while (true) {
            $line = yield;

            $parameters = ($this->fieldMapping)($line);
            if (isset($parameters)) {
                $this->preparedStatement->execute($parameters);
            }

            yield new GenericBucket($line);
        }
    }
}
