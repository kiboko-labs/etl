<?php

namespace Kiboko\Component\ETL\Loader;

use Akeneo\Bundle\BatchBundle\Entity\StepExecution;
use Akeneo\Bundle\BatchBundle\Step\StepInterface;

class AkeneoBatchStepLoader implements LoaderInterface
{
    /**
     * @var StepInterface
     */
    private $step;

    /**
     * @param StepInterface $step
     */
    public function __construct(StepInterface $step)
    {
        $this->step = $step;
    }

    public function load(iterable $source): iterable
    {
        $this->step->execute(new StepExecution());
    }
}
