<?php

namespace Kiboko\Component\ETL\Pipeline;

class PipelineBuilder
{
    public function build(
        array $configuration,
        ?PipelineRunnerInterface $runner = null
    ): PipelineInterface {
        $pipeline = new Pipeline($runner ?? new PipelineRunner());
        foreach ($configuration as $stage) {

        }
    }
}
