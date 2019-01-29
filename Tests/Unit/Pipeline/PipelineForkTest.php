<?php

namespace Kiboko\Component\ETL\Tests\Unit\Pipeline;

use Kiboko\Component\ETL\Flow\Transformer\CallableTransformer;
use Kiboko\Component\ETL\Flow\Transformer\ChildPipelineTransformer;
use Kiboko\Component\ETL\Pipeline\Pipeline;
use Kiboko\Component\ETL\Pipeline\PipelineInterface;
use Kiboko\Component\ETL\Pipeline\PipelineRunner;
use Kiboko\Component\ETL\Tests\Unit\IterableTestCase;

class PipelineForkTest extends IterableTestCase
{
    public function testChildPipeline()
    {
        $pipelineRunner = new PipelineRunner();

        $pipeline = new Pipeline($pipelineRunner,
            (function() {
                yield [
                    'foo' => '  lorem  ',
                    'bar' => '  ipsum  ',
                    'baz' => '  dolor  ',
                ];
                yield [
                    'foo' => '  lorem  ',
                    'bar' => '  ipsum  ',
                    'baz' => '  dolor  ',
                ];
                yield [
                    'foo' => '  lorem  ',
                    'bar' => '  ipsum  ',
                    'baz' => '  dolor  ',
                ];
            })()
        );

        $pipeline->fork(
            function(PipelineInterface $pipeline) {
                $pipeline->transform(new CallableTransformer(function($line) {
                    return array_map('trim', $line);
                }));
            },
            function(PipelineInterface $pipeline) {
                $pipeline->transform(new CallableTransformer(function($line) {
                    return array_map('str_rot13', $line);
                }));
            }
        );

        $this->assertIteration(
            new \ArrayIterator([
                [
                    'foo' => 'lorem',
                    'bar' => 'ipsum',
                    'baz' => 'dolor',
                ],
                [
                    'foo' => '  yberz  ',
                    'bar' => '  vcfhz  ',
                    'baz' => '  qbybe  ',
                ],
                [
                    'foo' => 'lorem',
                    'bar' => 'ipsum',
                    'baz' => 'dolor',
                ],
                [
                    'foo' => '  yberz  ',
                    'bar' => '  vcfhz  ',
                    'baz' => '  qbybe  ',
                ],
                [
                    'foo' => 'lorem',
                    'bar' => 'ipsum',
                    'baz' => 'dolor',
                ],
                [
                    'foo' => '  yberz  ',
                    'bar' => '  vcfhz  ',
                    'baz' => '  qbybe  ',
                ],
            ]),
            $pipeline->walk()
        );
    }
}
