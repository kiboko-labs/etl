<?php

namespace Kiboko\Component\ETL\Flow\Transformer;

use Kiboko\Component\ETL\Pipeline\EmptyBucket;
use Kiboko\Component\ETL\Pipeline\GenericBucket;

class LookupTransformer implements TransformerInterface
{
    /**
     * @var \PDOStatement
     */
    private $statement;

    /**
     * @var callable
     */
    private $callback;

    /**
     * @param \PDOStatement $statement
     * @param callable $callback
     */
    public function __construct(
        \PDOStatement $statement,
        callable $callback
    ) {
        $this->statement = $statement;
        $this->callback = $callback;
    }

    public function transform(): \Generator
    {
        while (true) {
            $line = yield;

            if ($line !== null) {
                yield ($this->callback)($line, $this->statement);
            } else {
                yield new EmptyBucket();
            }
        }
    }
}
