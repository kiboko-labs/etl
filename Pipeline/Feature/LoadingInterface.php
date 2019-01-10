<?php

namespace Kiboko\Component\ETL\Pipeline\Feature;

use Kiboko\Component\ETL\Flow\Loader\LoaderInterface;

interface LoadingInterface
{
    /**
     * @param LoaderInterface $loader
     *
     * @return $this
     */
    public function load(LoaderInterface $loader): LoadingInterface;
}
