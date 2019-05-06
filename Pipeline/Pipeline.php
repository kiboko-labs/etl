<?php

namespace Kiboko\Component\ETL\Pipeline;

use Kiboko\Component\ETL\Flow\Extractor\ExtractorInterface;
use Kiboko\Component\ETL\Flow\FlushableInterface;
use Kiboko\Component\ETL\Flow\Loader\LoaderInterface;
use Kiboko\Component\ETL\Flow\Transformer\TransformerInterface;
use Kiboko\Component\ETL\Pipeline\Bucket\AcceptanceAppendableResultBucket;
use Kiboko\Component\ETL\Pipeline\Feature\ExtractingInterface;
use Kiboko\Component\ETL\Pipeline\Feature\ForkingInterface;
use Kiboko\Component\ETL\Pipeline\Feature\LoadingInterface;
use Kiboko\Component\ETL\Pipeline\Feature\RunnableInterface;
use Kiboko\Component\ETL\Pipeline\Feature\TransformingInterface;
use Kiboko\Component\ETL\Pipeline\Feature\WalkableInterface;

class Pipeline implements PipelineInterface, ForkingInterface, WalkableInterface, RunnableInterface
{
    /**
     * @var \AppendIterator
     */
    private $source;

    /**
     * @var iterable
     */
    private $subject;

    /**
     * @var PipelineRunnerInterface
     */
    private $runner;

    public function __construct(PipelineRunnerInterface $runner, ?\Iterator $source = null)
    {
        $this->source = new \AppendIterator();
        $this->source->append($source ?? new \EmptyIterator());

        $this->subject = new \NoRewindIterator($this->source);
        $this->runner = $runner;
    }

    public function feed(...$data): void
    {
        $this->source->append(new \ArrayIterator($data));
    }

    public function fork(callable ...$builders): ForkingInterface
    {
        $runner = $this->runner;
        $handlers = [];
        foreach ($builders as $builder) {
            $handlers[] = $handler = new class(new Pipeline($runner)) {
                /** @var PipelineInterface */
                public $pipeline;
                /** @var \Iterator */
                public $consumer;

                public function __construct(PipelineInterface $pipeline)
                {
                    $this->pipeline = $pipeline;
                    $this->consumer = $pipeline->walk();
                    $this->consumer->rewind();
                }
            };

            $builder($handler->pipeline);
        }

        $this->subject = $this->runner->run($this->subject, (function(array $handlers) {
            while (true) {
                $line = yield;

                $bucket = new AcceptanceAppendableResultBucket();
                /** @var \Iterator $handler */
                foreach ($handlers as $handler) {
                    $handler->pipeline->feed($line);
                    $bucket->append(new \NoRewindIterator($handler->consumer));
                }

                yield $bucket;
            }
        })($handlers));

        return $this;
    }

    /**
     * @param ExtractorInterface $extractor
     *
     * @return $this
     */
    public function extract(ExtractorInterface $extractor): ExtractingInterface
    {
        $this->source->append($extractor->extract());

        if ($extractor instanceof FlushableInterface) {
            $this->source->append((function(FlushableInterface $flushable) {
                yield from $flushable->flush();
            })($extractor));
        }

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
                $main = $this->runner->run($this->subject, $transformer->transform())
            );

            $iterator->append((function(FlushableInterface $flushable) {
                yield from $flushable->flush();
            })($transformer));
        } else {
            $iterator = $this->runner->run($this->subject, $transformer->transform());
        }

        $this->subject = new \NoRewindIterator($iterator);

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
                $main = $this->runner->run($this->subject, $loader->load())
            );

            $iterator->append((function(FlushableInterface $flushable) {
                yield from $flushable->flush();
            })($loader));
        } else {
            $iterator = $this->runner->run($this->subject, $loader->load());
        }

        $this->subject = new \NoRewindIterator($iterator);

        return $this;
    }

    /**
     * @return \Iterator
     */
    public function walk(): \Iterator
    {
        yield from $this->subject;
    }

    /**
     * @return int
     */
    public function run(): int
    {
        return iterator_count($this->walk());
    }
}
