<?php

namespace Kiboko\Component\ETL\Pipeline\Feature;

use Kiboko\Component\ETL\Transformer\TransformerInterface;

interface TransformingInterface
{
    /**
     * @param TransformerInterface $transformer
     *
     * @return $this
     */
    public function transform(TransformerInterface $transformer): TransformingInterface;
}
