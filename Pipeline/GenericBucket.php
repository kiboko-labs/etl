<?php

namespace Kiboko\Component\ETL\Pipeline;

use Kiboko\Component\ETL\Pipeline\Bucket\AcceptanceResultBucket;

/**
 * @deprecated
 */
class GenericBucket extends AcceptanceResultBucket implements ResultBucketInterface
{
    public function getIterator()
    {
        /** @var array|\Traversable $acceptance */
        $acceptance = $this->walkAcceptance();
        if (is_array($acceptance)) {
            return new \ArrayIterator($acceptance);
        }

        return new \IteratorIterator($acceptance);
    }
}

trigger_error(
    strtr(
        'The class %deprecated% is deprecated, please use %replacement% instead',
        [
            '%deprecated%' => GenericBucket::class,
            '%replacement%' => AcceptanceResultBucket::class,
        ]
    ),
    E_USER_DEPRECATED
);
