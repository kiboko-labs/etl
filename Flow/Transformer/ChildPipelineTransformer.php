<?php

namespace Kiboko\Component\ETL\Flow\Transformer;

use Kiboko\Component\ETL\Flow\Extractor\IteratorExtractor;
use Kiboko\Component\ETL\Pipeline\AppendableBucket;
use Kiboko\Component\ETL\Pipeline\GenericBucket;
use Kiboko\Component\ETL\Pipeline\IteratorBucket;
use Kiboko\Component\ETL\Pipeline\Pipeline;
use Kiboko\Component\ETL\Pipeline\PipelineInterface;
use Kiboko\Component\ETL\Pipeline\PipelineRunnerInterface;

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
        $pipeline = new Pipeline($this->pipelineRunner);
        $pipeline->extract(
            new IteratorExtractor(
                $iterator = new AppendableBucket()
            )
        )

        ($this->builder)($pipeline);

        while ($line = yield) {
            $iterator->append($line);
            yield new IteratorBucket($pipeline->walk());
        }
    }
}
