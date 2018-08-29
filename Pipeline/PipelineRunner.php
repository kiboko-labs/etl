<?php

namespace Kiboko\Component\ETL\Pipeline;

class PipelineRunner implements PipelineRunnerInterface
{
    public function fork(callable $reduce, \Iterator $source, \Generator ...$async): \Iterator
    {
        $wrapper = new GeneratorWrapper();
        $wrapper->rewind($source, ...$async);

        while ($wrapper->valid($source, ...$async)) {
            yield $this->reduce($reduce, $wrapper->send($source->current(), ...$async));

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
            yield $generator->send($iterator->current());

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
