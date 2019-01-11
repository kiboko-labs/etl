<?php

namespace Kiboko\Component\ETL\Pipeline;

class MergeBucket implements ResultBucketInterface
{
    /**
     * @var ResultBucketInterface[]
     */
    private $buckets;

    /**
     * @param ResultBucketInterface[] $buckets
     */
    public function __construct(ResultBucketInterface... $buckets)
    {
        $this->buckets = $buckets;
    }

    public function append(ResultBucketInterface ...$buckets)
    {
        $this->buckets = array_merge(
            $this->buckets,
            $buckets
        );
    }

    public function getIterator()
    {
        $iterator = new \AppendIterator();
        foreach ($this->buckets as $child) {
            $iterator->append($child->getIterator());
        }
        return $iterator;
    }
}
