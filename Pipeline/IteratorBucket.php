<?php

namespace Kiboko\Component\ETL\Pipeline;

use Kiboko\Component\ETL\Pipeline\Bucket\AcceptanceIteratorResultBucket;

/**
 * @deprecated
 */
class IteratorBucket extends AcceptanceIteratorResultBucket implements ResultBucketInterface
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
            '%deprecated%' => IteratorBucket::class,
            '%replacement%' => AcceptanceIteratorResultBucket::class,
        ]
    ),
    E_USER_DEPRECATED
);
