<?php

namespace Kiboko\Component\ETL\Pipeline\Bucket;

class AcceptanceResultBucket implements AcceptanceResultBucketInterface
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

    public function walkAcceptance(): iterable
    {
        return new \ArrayIterator($this->values);
    }
}
