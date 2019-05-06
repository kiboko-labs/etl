<?php

namespace Kiboko\Component\ETL\Pipeline\Bucket;

class EmptyResultBucket implements
    AcceptanceResultBucketInterface,
    RejectionResultBucketInterface
{
    public function walkAcceptance(): iterable
    {
        return new \EmptyIterator();
    }

    public function walkRejection(): iterable
    {
        return new \EmptyIterator();
    }
}
