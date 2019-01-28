<?php

namespace Kiboko\Component\ETL\Pipeline;

use Kiboko\Component\ETL\Pipeline\Feature\ExtractingInterface;
use Kiboko\Component\ETL\Pipeline\Feature\ForkingInterface;
use Kiboko\Component\ETL\Pipeline\Feature\LoadingInterface;
use Kiboko\Component\ETL\Pipeline\Feature\TransformingInterface;

interface PipelineInterface extends ExtractingInterface, TransformingInterface, LoadingInterface
{
}
