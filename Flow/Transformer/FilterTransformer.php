<?php

namespace Kiboko\Component\ETL\Flow\Transformer;

use Kiboko\Component\ETL\Pipeline\Bucket\AcceptanceResultBucket;
use Kiboko\Component\ETL\Pipeline\Bucket\EmptyResultBucket;

class FilterTransformer implements TransformerInterface
{
    /**
     * @var callable
     */
    private $callback;

    /**
     * @param $callback
     */
    public function __construct(callable $callback)
    {
        $this->callback = $callback;
    }

    public function transform(): \Generator
    {
        $callback = $this->callback;
        while (true) {
            $line = yield;

            if (!$callback($line)) {
                yield new EmptyResultBucket();
                continue;
            }

            yield new AcceptanceResultBucket($line);
        }
    }
}
