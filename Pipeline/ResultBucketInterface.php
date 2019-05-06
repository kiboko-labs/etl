<?php

namespace Kiboko\Component\ETL\Pipeline;

use Kiboko\Component\ETL\Pipeline\Bucket\ResultBucketInterface as BaseResultBucketInterface;

/**
 * @deprecated
 */
interface ResultBucketInterface extends BaseResultBucketInterface, \IteratorAggregate
{
}

trigger_error(
    strtr(
        'The interface %deprecated% is deprecated, please use %replacement% instead',
        [
            '%deprecated%' => ResultBucketInterface::class,
            '%replacement%' => BaseResultBucketInterface::class,
        ]
    ),
    E_USER_DEPRECATED
);
