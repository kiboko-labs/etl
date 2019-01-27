<?php

namespace Kiboko\Component\ETL\Pipeline;

use Kiboko\Component\ETL\Flow\Extractor\ExtractorInterface;
use Kiboko\Component\ETL\Flow\FlushableInterface;
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
        if ($extractor instanceof FlushableInterface) {
            $iterator = new \AppendIterator();

            $iterator->append(
                $main = $extractor->extract()
            );

            $iterator->append((function(FlushableInterface $flushable) {
                yield from $flushable->flush();
            })($extractor));
        } else {
            $iterator = $extractor->extract();
        }

        $this->source = new \NoRewindIterator($iterator);

        return $this;
    }

    /**
     * @param TransformerInterface $transformer
     *
     * @return $this
     */
    public function transform(TransformerInterface $transformer): TransformingInterface
    {
        if ($transformer instanceof FlushableInterface) {
            $iterator = new \AppendIterator();

            $iterator->append(
                $main = $this->runner->run($this->source, $transformer->transform())
            );

            $iterator->append((function(FlushableInterface $flushable) {
                yield from $flushable->flush();
            })($transformer));
        } else {
            $iterator = $this->runner->run($this->source, $transformer->transform());
        }

        $this->source = new \NoRewindIterator($iterator);

        return $this;
    }

    /**
     * @param LoaderInterface $loader
     *
     * @return $this
     */
    public function load(LoaderInterface $loader): LoadingInterface
    {
        if ($loader instanceof FlushableInterface) {
            $iterator = new \AppendIterator();

            $iterator->append(
                $main = $this->runner->run($this->source, $loader->load())
            );

            $iterator->append((function(FlushableInterface $flushable) {
                yield from $flushable->flush();
            })($loader));
        } else {
            $iterator = $this->runner->run($this->source, $loader->load());
        }

        $this->source = new \NoRewindIterator($iterator);

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
