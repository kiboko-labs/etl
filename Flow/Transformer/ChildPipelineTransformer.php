<?php

namespace Kiboko\Component\ETL\Flow\Transformer;

use Kiboko\Component\ETL\Flow\Extractor\IteratorExtractor;
use Kiboko\Component\ETL\Iterator\IteratorLoggingWrapper;
use Kiboko\Component\ETL\Pipeline\AppendableBucket;
use Kiboko\Component\ETL\Pipeline\GenericBucket;
use Kiboko\Component\ETL\Pipeline\IteratorBucket;
use Kiboko\Component\ETL\Pipeline\Pipeline;
use Kiboko\Component\ETL\Pipeline\PipelineInterface;
use Kiboko\Component\ETL\Pipeline\PipelineRunnerInterface;
use Monolog\Handler\StreamHandler;
use Monolog\Logger;

class ChildPipelineTransformer implements TransformerInterface
{
    /**
     * @var PipelineRunnerInterface
     */
    private $pipelineRunner;

    /**
     * @var callable
     */
    private $builder;

    /**
     * @param PipelineRunnerInterface $pipelineRunner
     * @param callable $builder
     */
    public function __construct(
        PipelineRunnerInterface $pipelineRunner,
        callable $builder
    ) {
        $this->pipelineRunner = $pipelineRunner;
        $this->builder = $builder;
    }

    public function transform(): \Generator
    {
        $queue = new \SplQueue();
        $queue->setIteratorMode(\SplDoublyLinkedList::IT_MODE_DELETE);

        while (true) {
            $line = yield;

            $queue->enqueue($line);
            $queue->rewind();

            $pipeline = new Pipeline($this->pipelineRunner, $queue);
            ($this->builder)($pipeline);

            yield new IteratorBucket($pipeline->walk());
        }
    }
}
