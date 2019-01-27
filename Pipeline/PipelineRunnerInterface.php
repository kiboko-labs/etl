<?php

namespace Kiboko\Component\ETL\Pipeline;

interface PipelineRunnerInterface
{
    public function run(\Iterator $source, \Generator $async): \Iterator;
}
