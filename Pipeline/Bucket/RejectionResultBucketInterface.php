<?php

namespace Kiboko\Component\ETL\Pipeline\Bucket;

interface RejectionResultBucketInterface extends ResultBucketInterface
{
    public function walkRejection(): iterable;
}
