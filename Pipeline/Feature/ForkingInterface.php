<?php

namespace Kiboko\Component\ETL\Pipeline\Feature;

use Kiboko\Component\ETL\Pipeline\ForkBuilderInterface;

interface ForkingInterface
{
    /**
     * @param callable $reduce
     * @param ForkBuilderInterface[] $forkBuilders
     *
     * @return $this
     */
    public function fork(callable $reduce, ForkBuilderInterface ...$forkBuilders): ForkingInterface;

    /**
     * @return \Iterator
     */
    public function walk(): \Iterator;

    /**
     * @return int
     */
    public function run(): int;
}
