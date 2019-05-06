<?php

namespace Kiboko\Component\ETL\Flow\Transformer;

use Kiboko\Component\ETL\Pipeline\Bucket\AcceptanceResultBucket;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;

class NormalizationTransformer implements TransformerInterface
{
    /**
     * @var NormalizerInterface
     */
    private $normalizer;

    /**
     * @var string
     */
    private $format;

    /**
     * @var array
     */
    private $context;

    /**
     * @param NormalizerInterface $normalizer
     * @param string|null         $format
     * @param array               $context
     */
    public function __construct(NormalizerInterface $normalizer, string $format = null, array $context = [])
    {
        $this->normalizer = $normalizer;
        $this->format = $format;
        $this->context = $context;
    }

    public function transform(): \Generator
    {
        while (true) {
            $data = yield;

            yield new AcceptanceResultBucket($this->normalizer->normalize($data, $this->format, $this->context));
        }
    }
}
