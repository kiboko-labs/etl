<?php

namespace Kiboko\Component\ETL\Flow\Loader;

interface LoaderInterface
{
    /**
     * @return \Generator
     */
    public function load(): \Generator;
}
