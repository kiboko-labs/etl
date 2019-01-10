<?php

namespace Kiboko\Component\ETL\Flow\Extractor;

use Kiboko\Component\ETL\Pipeline\GenericBucket;

class PDOExtractor implements ExtractorInterface
{
    /**
     * @var \PDOStatement
     */
    private $preparedStatement;

    /**
     * @var array
     */
    private $arguments;

    public function __construct(\PDOStatement $preparedStatement, array $arguments = [])
    {
        $this->preparedStatement = $preparedStatement;
        $this->arguments = $arguments;
    }

    public function extract(): \Generator
    {
        $this->preparedStatement->execute($this->arguments);

        $this->preparedStatement->setFetchMode(\PDO::FETCH_ASSOC);
        foreach ($this->preparedStatement as $line) {
            yield new GenericBucket($line);
        }
    }
}
