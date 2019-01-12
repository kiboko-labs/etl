<?php

namespace Kiboko\Component\ETL\Mapper\Compiler;

use Kiboko\Component\ETL\Mapper\MapperInterface;

class CompilationContext
{
    /**
     * @var string
     */
    public $path;

    /**
     * @var string
     */
    public $namespace;

    /**
     * @var string
     */
    public $className;

    /**
     * @var MapperInterface[]
     */
    public $mappers;

    /**
     * @param string            $path
     * @param string            $namespace
     * @param string            $className
     * @param MapperInterface[] $mappers
     */
    public function __construct(?string $path, ?string $namespace, ?string $className, MapperInterface... $mappers)
    {
        $this->path = $path;
        $this->namespace = $namespace;
        $this->className = $className;
        $this->mappers = $mappers;
    }
}
