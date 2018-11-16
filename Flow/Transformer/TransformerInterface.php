<?php

namespace Kiboko\Component\ETL\Flow\Transformer;

interface TransformerInterface
{
    /**
     * Get the transformer handler.
     *
     * @return \Generator
     */
    public function transform(): \Generator;
}
