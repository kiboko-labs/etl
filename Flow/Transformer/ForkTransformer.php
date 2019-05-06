<?php

namespace Kiboko\Component\ETL\Flow\Transformer;

use Kiboko\Component\ETL\Exception\UnexpectedYieldedValueType;
use Kiboko\Component\ETL\Pipeline\Bucket\AcceptanceAppendableResultBucket;
use Kiboko\Component\ETL\Pipeline\Bucket\ResultBucketInterface;

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
            $coroutine = $coroutines[] = $transformer->transform();
            $coroutine->rewind();
        }

        while (true) {
            $line = yield;

            $mergeBucket = new AcceptanceAppendableResultBucket();

            foreach ($coroutines as $coroutine) {
                $coroutine->send($line);

                if (!$coroutine->valid()) {
                    break;
                }

                $bucket = $coroutine->current();

                if (!$bucket instanceof ResultBucketInterface) {
                    throw UnexpectedYieldedValueType::expectingType(
                        $coroutine,
                        ResultBucketInterface::class,
                        $bucket
                    );
                }

                $mergeBucket->append($bucket);

                $coroutine->next();
                if (!$coroutine->valid()) {
                    break;
                }
            }

            yield $mergeBucket;
        }
    }
}
