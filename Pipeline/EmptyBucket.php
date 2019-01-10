<?php

namespace Kiboko\Component\ETL\Pipeline;

class EmptyBucket implements ResultBucketInterface
{
    public function getIterator()
    {
        return new \EmptyIterator();
    }
}
