<?php

namespace Kiboko\Component\ETL\Pipeline;

interface ForkBuilderInterface
{
    public function __invoke(PipelineRunnerInterface $runner, \Iterator $source): PipelineInterface;
}
