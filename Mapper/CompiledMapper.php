<?php

namespace Kiboko\Component\ETL\Mapper;

use Kiboko\Component\ETL\Mapper\Compiler\CompilationContext;
use Kiboko\Component\ETL\Mapper\Compiler\Compiler;

class CompiledMapper implements MapperInterface
{
    /**
     * @var Compiler
     */
    private $compiler;

    /**
     * @var CompilationContext
     */
    private $compilationContext;

    /**
     * @var MapperInterface[]
     */
    private $mappers;

    /**
     * @var MapperInterface
     */
    private $compiledMapper;

    /**
     * @param Compiler          $compiler
     * @param string            $fqcn
     * @param string            $cachePath
     * @param MapperInterface[] $mappers
     */
    public function __construct(
        Compiler $compiler,
        string $fqcn,
        string $cachePath,
        MapperInterface... $mappers
    ) {
        $this->compiler = $compiler;

        $namespace = substr($fqcn, 0, strpos($fqcn, '\\') + 1);
        $className = substr($fqcn, strpos($fqcn, '\\') + 1);

        $this->compilationContext = new CompilationContext(
            $cachePath . $className . '.php',
            $namespace,
            $className,
            ...$mappers
        );
    }

    public function map(array $input): array
    {
        if ($this->compiledMapper === null) {
            $this->compiledMapper = $this->compiler->compile($this->compilationContext);
        }

        return $this->compiledMapper->map($input);
    }
}
