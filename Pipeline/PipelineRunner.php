<?php

namespace Kiboko\Component\ETL\Pipeline;

use Kiboko\Component\ETL\Exception\UnexpectedYieldedValueType;
use Kiboko\Component\ETL\Iterator\ResumableIterator;

class PipelineRunner implements PipelineRunnerInterface
{
    /**
     * @param \Iterator  $source
     * @param \Generator $coroutine
     *
     * @return \Iterator
     */
    public function run(\Iterator $source, \Generator $coroutine): \Iterator
    {
        return new ResumableIterator(function(\Iterator $source) use($coroutine) {
            $wrapper = new GeneratorWrapper();
            $wrapper->rewind($source, $coroutine);

            while ($wrapper->valid($source)) {
                $bucket = $coroutine->send($source->current());

                if (!$bucket instanceof Bucket\ResultBucketInterface) {
                    throw UnexpectedYieldedValueType::expectingType(
                        $coroutine,
                        Bucket\ResultBucketInterface::class,
                        $bucket
                    );
                }

                if ($bucket instanceof Bucket\RejectionResultBucketInterface) {
                    // TODO: handle the rejection pipeline
                }

                if (!$bucket instanceof Bucket\AcceptanceResultBucketInterface) {
                    throw UnexpectedYieldedValueType::expectingType(
                        $coroutine,
                        Bucket\AcceptanceResultBucketInterface::class,
                        $bucket
                    );
                }

                yield from $bucket->walkAcceptance();

                $wrapper->next($source, $coroutine);
            }
        }, $source);
    }
}
