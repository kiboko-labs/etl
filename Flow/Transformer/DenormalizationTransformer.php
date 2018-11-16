<?php

namespace Kiboko\Component\ETL\Flow\Transformer;

use Symfony\Component\Serializer\Normalizer\DenormalizerInterface;

class DenormalizationTransformer implements TransformerInterface
{
    /**
     * @var DenormalizerInterface
     */
    private $denormalizer;

    /**
     * @var string
     */
    private $class;

    /**
     * @var string|null
     */
    private $format;

    /**
     * @var array
     */
    private $context;

    /**
     * @param DenormalizerInterface $denormalizer
     * @param string                $class
     * @param string|null           $format
     * @param array                 $context
     */
    public function __construct(
        DenormalizerInterface $denormalizer,
        string $class,
        string $format = null,
        array $context = []
    ) {
        $this->denormalizer = $denormalizer;
        $this->class = $class;
        $this->format = $format;
        $this->context = $context;
    }

    public function transform(): \Generator
    {
        while ($data = yield) {
            yield $this->denormalizer->denormalize($data, $this->format, $this->context);
        }
    }
}
