<?php

namespace Kiboko\Component\ETL\Pipeline;

class GeneratorWrapper
{
    public function rewind(\Iterator ...$iterators): void
    {
        foreach ($iterators as $iterator) {
            $iterator->rewind();
        }
    }

    public function next(\Iterator ...$iterators): void
    {
        foreach ($iterators as $iterator) {
            $iterator->next();
        }
    }

    public function valid(\Iterator ...$iterators): bool
    {
        foreach ($iterators as $iterator) {
            if (!$iterator->valid()) {
                return false;
            }
        }

        return true;
    }

    public function send($value, \Generator ...$generators): \Generator
    {
        foreach ($generators as $generator) {
            yield $generator->send($value);
        }
    }
}
