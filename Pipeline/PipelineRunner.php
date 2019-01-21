<?php

namespace Kiboko\Component\ETL\Pipeline;

use Kiboko\Component\ETL\Exception\UnexpectedYieldedValueType;

class PipelineRunner implements PipelineRunnerInterface
{
    public function fork(callable $reduce, \Iterator $source, \Generator ...$async): \Iterator
    {
        $wrapper = new GeneratorWrapper();
        $wrapper->rewind($source, ...$async);

        while ($wrapper->valid($source, ...$async)) {
            $bucket = $this->reduce($reduce, $wrapper->send($source->current(), ...$async));

            if (!$bucket instanceof ResultBucketInterface) {
                throw new \UnexpectedValueException(strtr(
                    'Invalid yielded data, was expecting %expected%, got %actual%.',
                    [
                        '%expected%' => ResultBucketInterface::class,
                        '%actual%' => is_object($bucket) ? get_class($bucket) : gettype($bucket),
                    ]
                ));
            }

            yield from $bucket;

            $wrapper->next($source, ...$async);
        }
    }

    public function await(callable $reduce, \Iterator $source, \Generator ...$async): iterable
    {
        $wrapper = new GeneratorWrapper();
        $wrapper->rewind($source, ...$async);

        foreach ($source as $line) {
            if (!$wrapper->valid(...$async)) {
                break;
            }

            $results = [];
            $excluded = false;
            foreach ($async as $coroutine) {
                $coroutine->send($line);
                $excluded = $excluded || false === ($results[] = $coroutine->send($line));
            }

            if ($excluded === true) {
                continue;
            }

            yield $this->reduce($reduce, $results);
        }
    }

    public function pipe(\Iterator $iterator, \Generator ...$generators): \Iterator
    {
        $generator = array_shift($generators);

        if (count($generators) > 0) {
            return $this->pipe($this->run($iterator, $generator), ...$generators);
        }

        return $this->run($iterator, $generator);
    }

    public function run(\Iterator $iterator, \Generator $generator): \Iterator
    {
        $wrapper = new GeneratorWrapper();
        $wrapper->rewind($iterator, $generator);

        while ($wrapper->valid($iterator)) {
            $bucket = $generator->send($iterator->current());

            if (!$bucket instanceof ResultBucketInterface) {
                throw UnexpectedYieldedValueType::expectingType(
                    $generator,
                    ResultBucketInterface::class,
                    $bucket
                );
            }

            yield from $bucket;

            $wrapper->next($iterator, $generator);
        }
    }

    private function reduce(callable $function, iterable $iterable)
    {
        $current = null;
        foreach ($iterable as $key => $value) {
            $current = $function($current, $value, $key);
        }

        return $current;
    }
}
