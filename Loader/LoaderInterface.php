<?php

namespace Kiboko\Component\ETL\Loader;

interface LoaderInterface
{
    /**
     * @return \Generator
     */
    public function load(): \Generator;
}
