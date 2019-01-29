<?php

namespace Kiboko\Component\ETL\Flow\Transformer;

use Kiboko\Component\ETL\Pipeline\EmptyBucket;
use Kiboko\Component\ETL\Pipeline\GenericBucket;

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
                yield new EmptyBucket();
                continue;
            }

            yield new GenericBucket($line);
        }
    }
}
