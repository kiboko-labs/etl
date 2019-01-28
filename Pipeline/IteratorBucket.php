<?php

namespace Kiboko\Component\ETL\Pipeline;

class IteratorBucket implements ResultBucketInterface
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

    public function getIterator()
    {
        return $this->iterator;
    }
}
