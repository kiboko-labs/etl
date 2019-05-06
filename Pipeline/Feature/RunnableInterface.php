<?php

namespace Kiboko\Component\ETL\Pipeline\Feature;

interface RunnableInterface
{
    public function run(): int;
}
