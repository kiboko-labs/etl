<?php

namespace Kiboko\Component\ETL\Tests\Unit\Transformer;

use Kiboko\Component\ETL\Pipeline\Pipeline;
use Kiboko\Component\ETL\Pipeline\PipelineRunner;
use Kiboko\Component\ETL\Tests\Unit\IterableTestCase;

class ChildPipelineTransformerTest extends IterableTestCase
{
    public function testChildPipeline()
    {
        $pipeline = new Pipeline(
            $runner = new PipelineRunner()
        );
    }
}
