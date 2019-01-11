<?php

namespace Kiboko\Component\ETL\Flow\Transformer;

use Kiboko\Component\ETL\Pipeline\MergeBucket;

class ForkTransformer implements TransformerInterface
{
    /**
     * @var TransformerInterface[]
     */
    private $transformers;

    /**
     * @param TransformerInterface[] $transformers
     */
    public function __construct(TransformerInterface... $transformers)
    {
        $this->transformers = $transformers;
    }

    public function transform(): \Generator
    {
        /** @var \Generator[] $coroutines */
        $coroutines = [];
        foreach ($this->transformers as $transformer) {
            $coroutines[] = $transformer->transform();
        }

        while ($line = yield) {
            $bucket = new MergeBucket();

            foreach ($coroutines as $coroutine) {
                $bucket->append($coroutine->send($line));
            }

            yield $bucket;
        }
    }
}
