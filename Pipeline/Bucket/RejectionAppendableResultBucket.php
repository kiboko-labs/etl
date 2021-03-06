<?php

namespace Kiboko\Component\ETL\Pipeline\Bucket;

class RejectionAppendableResultBucket implements RejectionResultBucketInterface
{
    /**
     * @var \Iterator
     */
    private $iterator;

    /**
     * @param \Iterator ...$iterators
     */
    public function __construct(\Iterator ...$iterators)
    {
        $this->iterator = new \AppendIterator();
        foreach ($iterators as $iterator){
            $this->iterator->append($iterator);
        }
    }

    /**
     * @param \Iterator ...$iterators
     */
    public function append(\Iterator ...$iterators)
    {
        foreach ($iterators as $iterator){
            $this->iterator->append($iterator);
        }
    }

    public function walkRejection(): iterable
    {
        return $this->iterator;
    }
}
