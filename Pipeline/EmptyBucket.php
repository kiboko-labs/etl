<?php

namespace Kiboko\Component\ETL\Pipeline;

use \Kiboko\Component\ETL\Pipeline\Bucket\EmptyResultBucket;

/**
 * @deprecated
 */
class EmptyBucket extends EmptyResultBucket implements \IteratorAggregate
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
            '%deprecated%' => EmptyBucket::class,
            '%replacement%' => EmptyResultBucket::class,
        ]
    ),
    E_USER_DEPRECATED
);
