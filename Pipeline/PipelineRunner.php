<?php

namespace Kiboko\Component\ETL\Pipeline;

use Kiboko\Component\ETL\Exception\UnexpectedYieldedValueType;

class PipelineRunner implements PipelineRunnerInterface
{
    public function run(\Iterator $iterator, \Generator $generator): \Iterator
    {
        $wrapper = new GeneratorWrapper();
        $wrapper->rewind($iterator, $generator);

        while ($wrapper->valid($iterator)) {
            $bucket = $generator->send($iterator->current());
            
            if (!$bucket) {
                break;
            }
            
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
}
