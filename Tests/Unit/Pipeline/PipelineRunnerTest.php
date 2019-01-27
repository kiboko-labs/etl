<?php

namespace Kiboko\Component\ETL\Tests\Unit\Pipeline;

use Kiboko\Component\ETL\Pipeline\EmptyBucket;
use Kiboko\Component\ETL\Pipeline\GenericBucket;
use Kiboko\Component\ETL\Pipeline\PipelineRunner;
use Kiboko\Component\ETL\Tests\Unit\IterableTestCase;

class PipelineRunnerTest extends IterableTestCase
{
    public function providerRun()
    {
        // Test if pipeline can walk items, without adding or removing any item
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
                    yield new GenericBucket(strrev($item));
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

        // Test if pipeline can walk items, while removing some items
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
                        yield new GenericBucket(strrev($item));
                    } else {
                        yield new EmptyBucket();
                    }
                }
            },
            [
                'merol',
                'rolod',
                'tema',
            ]
        ];

        // Test if pipeline can walk items, while adding some items
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
                    yield new GenericBucket(
                        $item,
                        strrev($item)
                    );
                }
            },
            [
                'lorem',
                'merol',
                'ipsum',
                'muspi',
                'dolor',
                'rolod',
                'sit',
                'tis',
                'amet',
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
}
