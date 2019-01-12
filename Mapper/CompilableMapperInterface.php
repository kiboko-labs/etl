<?php

namespace Kiboko\Component\ETL\Mapper;

use PhpParser\Node;

interface CompilableMapperInterface extends MapperInterface
{
    /**
     * @return Node[]
     */
    public function compile(): array;
}
