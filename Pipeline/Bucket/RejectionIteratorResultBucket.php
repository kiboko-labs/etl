<?php

namespace Kiboko\Component\ETL\Pipeline\Bucket;

class RejectionIteratorResultBucket implements RejectionResultBucketInterface
{
    /**
     * @var \Iterator
     */
    private $iterator;

    /**
     * @param \Iterator $iterator
     */
    public function __construct(\Iterator $iterator)
    {
        $this->iterator = $iterator;
    }

    public function walkRejection(): iterable
    {
        return $this->iterator;
    }
}
