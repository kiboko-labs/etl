<?php

namespace Kiboko\Component\ETL\Tests;

use Kiboko\Component\ETL\Pipeline\Pipeline;
use Kiboko\Component\ETL\Pipeline\PipelineInterface;
use Kiboko\Component\ETL\Pipeline\PipelineRunnerInterface;
use Kiboko\Component\ETL\Transformer;
use Kiboko\Component\ETL\Loader;
use Kiboko\Component\ETL\Pipeline\ForkBuilderInterface;

class FooForkBuilder implements ForkBuilderInterface
{
    private $filename;

    /**
     * @param string $filename
     */
    public function __construct(string $filename)
    {
        $this->filename = $filename;
    }

    public function __invoke(PipelineRunnerInterface $runner, \Iterator $source): PipelineInterface
    {
        $pipeline = new Pipeline($runner, $source);

        $pipeline
            ->transform(new Transformer\FilterTransformer(function($item) {
                return true;
            }))
            ->load(new Loader\SplCSVLoader(new \SplFileObject($this->filename, 'w')))
        ;

        return $pipeline;
    }
}
