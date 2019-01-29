<?php

namespace Kiboko\Component\ETL\Pipeline\Feature;

interface ForkingInterface
{
    /**
     * @param callable[] $builders
     *
     * @return $this
     */
    public function fork(callable... $builders): ForkingInterface;
}
