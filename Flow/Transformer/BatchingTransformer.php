<?php

namespace Kiboko\Component\ETL\Flow\Transformer;

use Kiboko\Component\ETL\Flow\FlushableInterface;
use Kiboko\Component\ETL\Pipeline\AppendableBucket;
use Kiboko\Component\ETL\Pipeline\EmptyBucket;
use Kiboko\Component\ETL\Pipeline\ResultBucketInterface;

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
        $this->bucket = new EmptyBucket();
    }

    public function transform(): \Generator
    {
        $this->bucket = new AppendableBucket();
        $itemCount = 0;
        while (true) {
            $line = yield;

            $this->bucket->append($line);

            if ($this->batchSize <= ++$itemCount) {
                yield $this->bucket;
                $itemCount = 0;
                $this->bucket = new AppendableBucket();
            } else {
                yield new EmptyBucket();
            }
        }
    }

    public function flush(): ResultBucketInterface
    {
        return $this->bucket;
    }
}
