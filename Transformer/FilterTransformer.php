<?php

namespace Kiboko\Component\ETL\Transformer;

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
        while ($line = yield) {
            if (!$callback($line)) {
                continue;
            }

            yield $line;
        }
    }
}
