<?php

namespace Kiboko\Component\ETL\Pipeline\Bucket;

class RejectionResultBucket implements RejectionResultBucketInterface
{
    /**
     * @var array
     */
    private $values;

    /**
     * @param mixed[] $values
     */
    public function __construct(...$values)
    {
        $this->values = $values;
    }

    public function walkRejection(): iterable
    {
        return new \ArrayIterator($this->values);
    }
}
