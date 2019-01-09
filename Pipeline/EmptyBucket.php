<?php

namespace Kiboko\Component\ETL\Pipeline;

use Traversable;

class EmptyBucket implements ResultBucketInterface
{
    public function getIterator()
    {
        return new \EmptyIterator();
    }
}
