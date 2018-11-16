<?php

namespace Kiboko\Component\ETL\Pipeline;

use Kiboko\Component\ETL\Flow\Extractor\ExtractorInterface;
use Kiboko\Component\ETL\Flow\Loader\LoaderInterface;
use Kiboko\Component\ETL\Flow\Transformer\TransformerInterface;
use Kiboko\Component\ETL\Pipeline\Feature\ExtractingInterface;
use Kiboko\Component\ETL\Pipeline\Feature\ForkingInterface;
use Kiboko\Component\ETL\Pipeline\Feature\LoadingInterface;
use Kiboko\Component\ETL\Pipeline\Feature\TransformingInterface;

class Pipeline implements PipelineInterface
{
    /**
     * @var iterable
     */
    private $source;

    /**
     * @var PipelineRunnerInterface
     */
    private $runner;

    public function __construct(PipelineRunnerInterface $runner, ?\Iterator $source = null)
    {
        $this->source = $source ?? new \EmptyIterator();
        $this->runner = $runner;
    }

    /**
     * @param ExtractorInterface $extractor
     *
     * @return $this
     */
    public function extract(ExtractorInterface $extractor): ExtractingInterface
    {
        $this->source = new \NoRewindIterator(
            $iterator = $extractor->extract()
        );

        $iterator->rewind();

        return $this;
    }

    /**
     * @param TransformerInterface $transformer
     *
     * @return $this
     */
    public function transform(TransformerInterface $transformer): TransformingInterface
    {
        $this->source = new \NoRewindIterator(
            $iterator = $this->runner->run($this->source, $transformer->transform())
        );

        $iterator->rewind();

        return $this;
    }

    /**
     * @param LoaderInterface $loader
     *
     * @return $this
     */
    public function load(LoaderInterface $loader): LoadingInterface
    {
        $this->source = new \NoRewindIterator(
            $iterator = $this->runner->run($this->source, $loader->load())
        );

        $iterator->rewind();

        return $this;
    }

    /**
     * @param callable $reduce
     * @param ForkBuilderInterface[] $forkBuilders
     *
     * @return $this
     */
    public function fork(callable $reduce, ForkBuilderInterface ...$forkBuilders): ForkingInterface
    {
        $forks = array_map(function(ForkBuilderInterface $builder) {
            yield from ($builder($this->runner, $this->source))->walk();
        }, $forkBuilders);

        $this->source = $this->runner->await($reduce, $this->source, ...$forks);

        return $this;
    }

    /**
     * @return \Iterator
     */
    public function walk(): \Iterator
    {
        yield from $this->source;
    }

    /**
     * @return int
     */
    public function run(): int
    {
        return iterator_count($this->walk());
    }
}
