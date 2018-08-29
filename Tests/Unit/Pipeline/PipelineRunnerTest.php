<?php

namespace Kiboko\Component\ETL\Tests\Unit\Pipeline;

use Kiboko\Component\ETL\Pipeline\PipelineRunner;
use Kiboko\Component\ETL\Tests\Unit\IterableTestCase;

class PipelineRunnerTest extends IterableTestCase
{
    public function providerRun()
    {
        yield [
            new \ArrayIterator([
                'lorem',
                'ipsum',
                'dolor',
                'sit',
                'amet',
            ]),
            function() {
                while ($item = yield) {
                    yield strrev($item);
                }
            },
            [
                'merol',
                'muspi',
                'rolod',
                'tis',
                'tema',
            ]
        ];

        yield [
            new \ArrayIterator([
                'lorem',
                'ipsum',
                'dolor',
                'sit',
                'amet',
            ]),
            function() {
                while ($item = yield) {
                    static $i = 0;
                    if ($i++ % 2 === 0) {
                        yield strrev($item);
                    } else {
                        yield;
                    }
                }
            },
            [
                'merol',
                null,
                'rolod',
                null,
                'tema',
            ]
        ];
    }

    /**
     * @param \Iterator $source
     * @param callable  $callback
     * @param array     $expected
     *
     * @dataProvider providerRun
     */
    public function testRun(\Iterator $source, callable $callback, array $expected)
    {
        $run = new PipelineRunner();

        $it = $run->run($source, $callback());

        $this->assertIteration(new \ArrayIterator($expected), $it);
    }

    public function providerFork()
    {
        yield [
            new \ArrayIterator([
                'lorem',
                'ipsum',
                'dolor',
                'sit',
                'amet',
            ]),
            function() {
                while ($item = yield) {
                    yield $item;
                }
            },
            function() {
                while ($item = yield) {
                    yield strrev($item);
                }
            },
            [
                ['lorem', 'merol'],
                ['ipsum', 'muspi'],
                ['dolor', 'rolod'],
                ['sit', 'tis'],
                ['amet', 'tema'],
            ]
        ];

        yield [
            new \ArrayIterator([
                'lorem',
                'ipsum',
                'dolor',
                'sit',
                'amet',
            ]),
            function() {
                while ($item = yield) {
                    static $i = 0;

                    if (($i++ % 2) === 0) {
                        yield $item;
                    } else {
                        yield;
                    }
                }
            },
            function() {
                while ($item = yield) {
                    yield strrev($item);
                }
            },
            [
                ['lorem', 'merol'],
                [null, 'muspi'],
                ['dolor', 'rolod'],
                [null, 'tis'],
                ['amet', 'tema'],
            ]
        ];
    }

    /**
     * @param \Iterator $source
     * @param callable  $callback1
     * @param callable  $callback2
     * @param array     $expected
     *
     * @dataProvider providerFork
     */
    public function testFork(\Iterator $source, callable $callback1, callable $callback2, array $expected)
    {
        $run = new PipelineRunner();

        $it = $run->fork(function($current, $next) {
            if ($current === null) {
                return [
                    $next,
                ];
            }

            array_push($current, $next);

            return $current;
        }, $source, $callback1(), $callback2());

        $this->assertIteration(new \ArrayIterator($expected), $it);
    }

    public function providerPipe()
    {
        yield [
            new \ArrayIterator([
                'lorem',
                'ipsum',
                'dolor',
                'sit',
                'amets',
            ]),
            [
                ' 1merol1 ',
                ' 1muspi1 ',
                ' 1rolod1 ',
                ' 11tis11 ',
                ' 1stema1 ',
            ]
        ];
    }

    /**
     * @param \Iterator $source
     * @param array     $expected
     *
     * @dataProvider providerPipe
     */
    public function testPipe(\Iterator $source, array $expected)
    {
        $run = new PipelineRunner();

        $it = $run->pipe($source, (function() {
            while ($item = yield) {
                yield strrev($item);
            }
        })(), (function() {
            while ($item = yield) {
                yield str_pad($item, 7, '1', STR_PAD_BOTH);
            }
        })(), (function() {
            while ($item = yield) {
                yield str_pad($item, 9, ' ', STR_PAD_BOTH);
            }
        })());

        $this->assertIteration(new \ArrayIterator($expected), $it);
    }
}
