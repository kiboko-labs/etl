<?php

namespace Kiboko\Component\ETL\Flow\Transformer;

use Kiboko\Component\ETL\Pipeline\Bucket\AcceptanceResultBucket;

class CallableTransformer implements TransformerInterface
{
    /**
     * @var callable
     */
    private $callback;

    /**
     * @param callable $callback
     */
    public function __construct(
        callable $callback
    ) {
        $this->callback = $callback;
    }

    public function transform(): \Generator
    {
        while (true) {
            $line = yield;

            yield new AcceptanceResultBucket(($this->callback)($line));
        }
    }
}
