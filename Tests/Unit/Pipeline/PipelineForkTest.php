<?php

namespace Kiboko\Component\ETL\Tests\Unit\Pipeline;

use Kiboko\Component\ETL\Flow\Extractor\IteratorExtractor;
use Kiboko\Component\ETL\Flow\Transformer\CallableTransformer;
use Kiboko\Component\ETL\Pipeline\Pipeline;
use Kiboko\Component\ETL\Pipeline\PipelineInterface;
use Kiboko\Component\ETL\Pipeline\PipelineRunner;
use Kiboko\Component\ETL\Tests\Unit\IterableTestCase;

class PipelineForkTest extends IterableTestCase
{
    public function provideTwoBranchesWithTrimAmdRot13()
    {
        yield [
            (function() {
                yield [
                    'foo' => '  lorem  ',
                    'bar' => '  ipsum  ',
                    'baz' => '  dolor  ',
                ];
                yield [
                    'foo' => '  sit  ',
                    'bar' => '  amet  ',
                    'baz' => '  consecutir  ',
                ];
                yield [
                    'foo' => '  lorem  ',
                    'bar' => '  ipsum  ',
                    'baz' => '  dolor  ',
                ];
            })(),
            new \ArrayIterator([
                [
                    'foo' => 'lorem',
                    'bar' => 'ipsum',
                    'baz' => 'dolor',
                ],
                [
                    'foo' => 'yberz',
                    'bar' => 'vcfhz',
                    'baz' => 'qbybe',
                ],
                [
                    'foo' => 'sit',
                    'bar' => 'amet',
                    'baz' => 'consecutir',
                ],
                [
                    'foo' => 'yberz',
                    'bar' => 'vcfhz',
                    'baz' => 'qbybe',
                ],
                [
                    'foo' => 'lorem',
                    'bar' => 'ipsum',
                    'baz' => 'dolor',
                ],
                [
                    'foo' => 'yberz',
                    'bar' => 'vcfhz',
                    'baz' => 'qbybe',
                ],
            ])
        ];
        yield [
            new \ArrayIterator([
                [
                    'foo' => '  lorem  ',
                    'bar' => '  ipsum  ',
                    'baz' => '  dolor  ',
                ],
                [
                    'foo' => '  lorem  ',
                    'bar' => '  ipsum  ',
                    'baz' => '  dolor  ',
                ],
                [
                    'foo' => '  lorem  ',
                    'bar' => '  ipsum  ',
                    'baz' => '  dolor  ',
                ],
            ]),
            new \ArrayIterator([
                [
                    'foo' => 'lorem',
                    'bar' => 'ipsum',
                    'baz' => 'dolor',
                ],
                [
                    'foo' => 'yberz',
                    'bar' => 'vcfhz',
                    'baz' => 'qbybe',
                ],
                [
                    'foo' => 'lorem',
                    'bar' => 'ipsum',
                    'baz' => 'dolor',
                ],
                [
                    'foo' => 'yberz',
                    'bar' => 'vcfhz',
                    'baz' => 'qbybe',
                ],
                [
                    'foo' => 'lorem',
                    'bar' => 'ipsum',
                    'baz' => 'dolor',
                ],
                [
                    'foo' => 'yberz',
                    'bar' => 'vcfhz',
                    'baz' => 'qbybe',
                ],
            ])
        ];
    }

    public function provideOneBranchWithTrimAmdRot13()
    {
        yield [
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
            })(),
            new \ArrayIterator([
                [
                    'foo' => 'lorem',
                    'bar' => 'ipsum',
                    'baz' => 'dolor',
                ],
                [
                    'foo' => 'lorem',
                    'bar' => 'ipsum',
                    'baz' => 'dolor',
                ],
                [
                    'foo' => 'lorem',
                    'bar' => 'ipsum',
                    'baz' => 'dolor',
                ],
            ])
        ];
        yield [
            new \ArrayIterator([
                [
                    'foo' => '  lorem  ',
                    'bar' => '  ipsum  ',
                    'baz' => '  dolor  ',
                ],
                [
                    'foo' => '  lorem  ',
                    'bar' => '  ipsum  ',
                    'baz' => '  dolor  ',
                ],
                [
                    'foo' => '  lorem  ',
                    'bar' => '  ipsum  ',
                    'baz' => '  dolor  ',
                ],
            ]),
            new \ArrayIterator([
                [
                    'foo' => 'lorem',
                    'bar' => 'ipsum',
                    'baz' => 'dolor',
                ],
                [
                    'foo' => 'lorem',
                    'bar' => 'ipsum',
                    'baz' => 'dolor',
                ],
                [
                    'foo' => 'lorem',
                    'bar' => 'ipsum',
                    'baz' => 'dolor',
                ],
            ])
        ];
    }

    /**
     * @dataProvider provideTwoBranchesWithTrimAmdRot13
     */
    public function testChildPipelineFromSource(\Iterator $source, \Iterator $expected)
    {
        $pipelineRunner = new PipelineRunner();

        $pipeline = new Pipeline($pipelineRunner, $source);

        $pipeline->fork(
            function(PipelineInterface $pipeline) {
                $pipeline->transform(new CallableTransformer(function($line) {
                    return array_map('trim', $line);
                }));
            },
            function(PipelineInterface $pipeline) {
                $pipeline
                    ->transform(
                        new CallableTransformer(function($line) {
                            return array_map('str_rot13', $line);
                        })
                    )
                    ->transform(
                        new CallableTransformer(function($line) {
                            return array_map('trim', $line);
                        })
                    );
            }
        );

        $this->assertIteration(
            $expected,
            $pipeline->walk()
        );
    }

    /**
     * @dataProvider provideOneBranchWithTrimAmdRot13
     */
    public function testSingleChildPipelineFromSource(\Iterator $source, \Iterator $expected)
    {
        $pipelineRunner = new PipelineRunner();

        $pipeline = new Pipeline($pipelineRunner, $source);

        $pipeline->fork(
            function(PipelineInterface $pipeline) {
                $pipeline->transform(new CallableTransformer(function($line) {
                    return array_map('trim', $line);
                }));
            }
        );

        $this->assertIteration(
            $expected,
            $pipeline->walk()
        );
    }

    /**
     * @dataProvider provideTwoBranchesWithTrimAmdRot13
     */
    public function testChildPipelineFromExtractor(\Iterator $source, \Iterator $expected)
    {
        $pipelineRunner = new PipelineRunner();

        $pipeline = new Pipeline($pipelineRunner);

        $pipeline
            ->extract(new IteratorExtractor($source))
            ->fork(
                function(PipelineInterface $pipeline) {
                    $pipeline->transform(new CallableTransformer(function($line) {
                        return array_map('trim', $line);
                    }));
                },
                function(PipelineInterface $pipeline) {
                    $pipeline
                        ->transform(
                            new CallableTransformer(function($line) {
                                return array_map('str_rot13', $line);
                            })
                        )
                        ->transform(
                            new CallableTransformer(function($line) {
                                return array_map('trim', $line);
                            })
                        );
                }
            );

        $this->assertIteration(
            $expected,
            $pipeline->walk()
        );
    }
}
