<?php

namespace Kiboko\Component\ETL\Flow\Transformer;

use Kiboko\Component\ETL\Pipeline\MergeBucket;
use Kiboko\Component\ETL\Pipeline\ResultBucketInterface;

class ForkTransformer implements TransformerInterface
{
    /**
     * @var TransformerInterface[]
     */
    private $transformers;

    /**
     * @param TransformerInterface[] $transformers
     */
    public function __construct(TransformerInterface... $transformers)
    {
        $this->transformers = $transformers;
    }

    public function transform(): \Generator
    {
        /** @var \Generator[] $coroutines */
        $coroutines = [];
        foreach ($this->transformers as $transformer) {
            $coroutine = $coroutines[] = $transformer->transform();
            $coroutine->rewind();
        }

        while ($line = yield) {
            $mergeBucket = new MergeBucket();

            foreach ($coroutines as $coroutine) {
                $coroutine->send($line);

                if (!$coroutine->valid()) {
                    break;
                }

                $bucket = $coroutine->current();

                if (!$bucket instanceof ResultBucketInterface) {
                    $re = new \ReflectionGenerator($coroutine);

                    throw new \UnexpectedValueException(strtr(
                        'Invalid yielded data, was expecting %expected%, got %actual%. Coroutine declared in %function%, running in %file%:%line%.',
                        [
                            '%expected%' => ResultBucketInterface::class,
                            '%actual%' => is_object($bucket) ? get_class($bucket) : gettype($bucket),
                            '%function%' => $re->getFunction() instanceof \ReflectionMethod ?
                                $re->getFunction()->getDeclaringClass()->getName() . '::' . $re->getFunction()->getName() :
                                $re->getFunction()->getName(),
                            '%file%' => $re->getExecutingFile(),
                            '%line%' => $re->getExecutingLine(),
                        ]
                    ));
                }

                $mergeBucket->append($bucket);

                $coroutine->next();
                if (!$coroutine->valid()) {
                    break;
                }
            }

            yield $mergeBucket;
        }
    }
}
