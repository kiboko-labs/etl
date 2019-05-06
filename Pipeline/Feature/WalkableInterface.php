<?php

namespace Kiboko\Component\ETL\Pipeline\Feature;

interface WalkableInterface
{
    public function walk(): \Iterator;
}
