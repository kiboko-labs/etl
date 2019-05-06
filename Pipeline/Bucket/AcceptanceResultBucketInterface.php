<?php

namespace Kiboko\Component\ETL\Pipeline\Bucket;

interface AcceptanceResultBucketInterface extends ResultBucketInterface
{
    public function walkAcceptance(): iterable;
}
