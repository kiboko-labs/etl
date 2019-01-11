<?php

namespace Kiboko\Component\ETL\Pipeline;

class MergeBucket implements ResultBucketInterface
{
    /**
     * @var \Iterator[]
     */
    private $iterators;

    /**
     * @param \Iterator[] $iterators
     */
    public function __construct(\Iterator... $iterators)
    {
        $this->iterators = $iterators;
    }

    public function append(\Iterator ...$iterators)
    {
        $this->iterators = array_merge(
            $this->iterators,
            $iterators
        );
    }

    public function getIterator()
    {
        $iterator = new \AppendIterator();
        foreach ($this->iterators as $child) {
            $iterator->append($child);
        }
        return $iterator;
    }
}
