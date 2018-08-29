<?php

namespace Kiboko\Component\ETL\Transformer;

interface TransformerInterface
{
    /**
     * Get the transformer handler.
     *
     * @return \Generator
     */
    public function transform(): \Generator;
}
