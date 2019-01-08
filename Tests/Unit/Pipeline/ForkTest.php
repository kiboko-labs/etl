<?php

namespace Kiboko\Component\ETL\Tests\Unit\Pipeline;

use Kiboko\Component\ETL\Flow\Extractor\IteratorExtractor;
use Kiboko\Component\ETL\Pipeline\ForkBuilderInterface;
use Kiboko\Component\ETL\Pipeline\Pipeline;
use Kiboko\Component\ETL\Pipeline\PipelineInterface;
use Kiboko\Component\ETL\Pipeline\PipelineRunner;
use Kiboko\Component\ETL\Pipeline\PipelineRunnerInterface;
use Kiboko\Component\ETL\Tests\Unit\IterableTestCase;

class ForkTest extends IterableTestCase
{
    public function provider()
    {
        yield [
            // Data source
            new \ArrayIterator([
                [
                    'foo' => '    Lorem   ',
                    'bar' => '    ipsum   ',
                ],
                [
                    'foo' => '  dolor',
                    'bar' => 'sit  ',
                ],
                [
                    'foo' => ' amet ',
                    'bar' => '       consecutir',
                ],
            ]),
            // first fork transformation
            new \ArrayIterator([
                [
                    'foo' => 'Lorem',
                    'bar' => '    ipsum   ',
                ],
                [
                    'foo' => 'dolor',
                    'bar' => 'sit  ',
                ],
                [
                    'foo' => 'amet',
                    'bar' => '       consecutir',
                ],
            ]),
            // second fork transformation
            new \ArrayIterator([
                [
                    'foo' => '    Lorem   ',
                    'bar' => 'ipsum',
                ],
                [
                    'foo' => '  dolor',
                    'bar' => 'sit',
                ],
                [
                    'foo' => ' amet ',
                    'bar' => 'consecutir',
                ],
            ]),
            // resulting transformation
            new \ArrayIterator([
                [
                    'foo' => 'Lorem',
                    'bar' => 'ipsum',
                ],
                [
                    'foo' => 'dolor',
                    'bar' => 'sit',
                ],
                [
                    'foo' => 'amet',
                    'bar' => 'consecutir',
                ],
            ])
        ];
    }

    private function buildPipelineMock(PipelineRunnerInterface $runner, \Iterator $expected): PipelineInterface
    {
        $mock = $this->getMockBuilder(Pipeline::class)
            ->setMethods(['walk'])
            ->setConstructorArgs([$runner])
            ->getMock();

        $mock->method('walk')
            ->with()
            ->willReturn($expected);

        return $mock;
    }

    private function buildForkBuilderMock(PipelineRunnerInterface $runner, \Iterator $expected): ForkBuilderInterface
    {
        $mock = $this->getMockBuilder(ForkBuilderInterface::class)
            ->setMethods(['__invoke'])
            ->getMock();

        $mock->method('__invoke')
            ->with($runner)
            ->willReturn($this->buildPipelineMock($runner, $expected));

        return $mock;
    }

    /**
     * @dataProvider provider
     */
    public function testFork(\Iterator $source, \Iterator $firstTransformation, \Iterator $secondTransformation, \Iterator $expected)
    {
        $this->markTestSkipped('Pipeline forks are not implemented yet.');
        $runner = new PipelineRunner();
        $pipeline = new Pipeline($runner);

        $pipeline->extract(new IteratorExtractor($source));

        $pipeline->fork(
            function($current, $value, $key) {
                return $value;
            },
            $this->buildForkBuilderMock($runner, $firstTransformation),
            $this->buildForkBuilderMock($runner, $secondTransformation)
        );

        $this->assertIteration($expected, $pipeline->walk());
    }
}
