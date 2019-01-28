<?php

namespace Kiboko\Component\ETL\Tests\Unit\Transformer;

use Kiboko\Component\ETL\Flow\Transformer\CallableTransformer;
use Kiboko\Component\ETL\Flow\Transformer\ChildPipelineTransformer;
use Kiboko\Component\ETL\Pipeline\PipelineInterface;
use Kiboko\Component\ETL\Pipeline\PipelineRunner;
use Kiboko\Component\ETL\Tests\Unit\IterableTestCase;

class ChildPipelineTransformerTest extends IterableTestCase
{
    public function testChildPipeline()
    {
        $pipelineRunner = new PipelineRunner();

        $transformer = new ChildPipelineTransformer(
            $pipelineRunner,
            function(PipelineInterface $pipeline) {
                $pipeline->transform(new CallableTransformer(function($line) {
                    return array_map('trim', $line);
                }));
            }
        );

        $this->assertIteration(
            new \ArrayIterator([
                [
                    'foo' => 'lorem',
                    'bar' => 'ipsum',
                    'baz' => 'dolor',
                ]
            ]),
            $pipelineRunner->run(
                (function() {
                    yield [
                        'foo' => '  lorem  ',
                        'bar' => '  ipsum  ',
                        'baz' => '  dolor  ',
                    ];
                })(),
                $transformer->transform()
            )
        );
    }
}
