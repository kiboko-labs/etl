<?php

namespace Kiboko\Component\ETL\Transformer;

class CallableTransformer implements TransformerInterface
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
            yield $callback($line);
        }
    }
}
