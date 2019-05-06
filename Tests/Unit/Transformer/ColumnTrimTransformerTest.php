<?php

namespace Kiboko\Component\ETL\Tests\Unit\Transformer;

use Kiboko\Component\ETL\Pipeline\PipelineRunner;
use Kiboko\Component\ETL\Tests\Unit\IterableTestCase;
use Kiboko\Component\ETL\Flow\Transformer\ColumnTrimTransformer;

class ColumnTrimTransformerTest extends IterableTestCase
{
    public function testTransform()
    {
        $transformer = new ColumnTrimTransformer(['foo']);

        $pipelineRunner = new PipelineRunner();

        $this->assertIteration(
            new \ArrayIterator([
                [
                    'foo' => 'lorem',
                    'bar' => '  ipsum',
                    'baz' => '  dolor  ',
                ],
                [
                    'foo' => 'lorem',
                    'bar' => 'ipsum  ',
                    'baz' => '  dolor  ',
                ],
                [
                    'foo' => 'lorem',
                    'bar' => '  ipsum',
                    'baz' => 'dolor',
                ],
            ]),
            $pipelineRunner->run(
                (function() {
                    yield [
                        'foo' => '  lorem  ',
                        'bar' => '  ipsum',
                        'baz' => '  dolor  ',
                    ];
                    yield [
                        'foo' => '  lorem  ',
                        'bar' => 'ipsum  ',
                        'baz' => '  dolor  ',
                    ];
                    yield [
                        'foo' => '  lorem  ',
                        'bar' => '  ipsum',
                        'baz' => 'dolor',
                    ];
                })(),
                $transformer->transform()
            )
        );
    }
}
