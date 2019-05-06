<?php

namespace Kiboko\Component\ETL\Flow;

use Kiboko\Component\ETL\Pipeline\Bucket\ResultBucketInterface;

interface FlushableInterface
{
    public function flush(): ResultBucketInterface;
}
