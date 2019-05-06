<?php

namespace Kiboko\Component\ETL\Flow\Transformer;

use Kiboko\Component\ETL\Flow\FlushableInterface;
use Kiboko\Component\ETL\Pipeline\Bucket\AcceptanceAppendableResultBucket;
use Kiboko\Component\ETL\Pipeline\Bucket\EmptyResultBucket;
use Kiboko\Component\ETL\Pipeline\Bucket\ResultBucketInterface;

class BatchingTransformer implements TransformerInterface, FlushableInterface
{
    /**
     * @var int
     */
    private $batchSize;

    /**
     * @var ResultBucketInterface
     */
    private $bucket;

    /**
     * @param int $batchSize
     */
    public function __construct(int $batchSize)
    {
        $this->batchSize = $batchSize;
        $this->bucket = new EmptyResultBucket();
    }

    public function transform(): \Generator
    {
        $this->bucket = new AcceptanceAppendableResultBucket();
        $itemCount = 0;
        while (true) {
            $line = yield;

            $this->bucket->append($line);

            if ($this->batchSize <= ++$itemCount) {
                yield $this->bucket;
                $itemCount = 0;
                $this->bucket = new AcceptanceAppendableResultBucket();
            } else {
                yield new EmptyResultBucket();
            }
        }
    }

    public function flush(): ResultBucketInterface
    {
        return $this->bucket;
    }
}
