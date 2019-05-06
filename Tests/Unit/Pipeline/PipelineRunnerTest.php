<?php

namespace Kiboko\Component\ETL\Tests\Unit\Pipeline;

use Kiboko\Component\ETL\Pipeline\Bucket\AcceptanceResultBucket;
use Kiboko\Component\ETL\Pipeline\Bucket\EmptyResultBucket;
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
                    yield new AcceptanceResultBucket(strrev($item));
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
                        yield new AcceptanceResultBucket(strrev($item));
                    } else {
                        yield new EmptyResultBucket();
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
                    yield new AcceptanceResultBucket(
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
