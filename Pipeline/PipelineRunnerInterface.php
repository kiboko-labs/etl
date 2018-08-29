<?php

namespace Kiboko\Component\ETL\Pipeline;

interface PipelineRunnerInterface
{
    public function fork(callable $reduce, \Iterator $source, \Generator ...$async): \Iterator;

    public function await(callable $reduce, \Iterator $source, \Generator ...$async): iterable;

    public function pipe(\Iterator $source, \Generator ...$async): \Iterator;

    public function run(\Iterator $source, \Generator $async): \Iterator;
}
