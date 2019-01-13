<?php

namespace Kiboko\Component\ETL\Flow;

use Kiboko\Component\ETL\Pipeline\ResultBucketInterface;

interface FlushableInterface
{
    public function flush(): ResultBucketInterface;
}
