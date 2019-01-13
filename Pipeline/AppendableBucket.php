<?php

namespace Kiboko\Component\ETL\Pipeline;

class AppendableBucket implements ResultBucketInterface
{
    /**
     * @var array
     */
    private $values;

    /**
     * @param mixed[] $values
     */
    public function __construct(...$values)
    {
        $this->values = $values;
    }

    public function append(...$values)
    {
        $this->values = array_merge(
            $this->values,
            $values
        );
    }

    public function getIterator()
    {
        return new \ArrayIterator([
            $this->values
        ]);
    }
}
