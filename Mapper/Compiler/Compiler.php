<?php

namespace Kiboko\Component\ETL\Mapper\Compiler;

use Kiboko\Component\ETL\Mapper\CompilableMapperInterface;
use Kiboko\Component\ETL\Mapper\MapperInterface;
use PhpParser\BuilderFactory;
use PhpParser\Node;
use PhpParser\PrettyPrinter;

class Compiler
{
    public function compile(CompilationContext $context)
    {
        $namespace = $context->namespace ?? 'Kiboko\\Component\\ETL\\Compiled\\';
        $className = $context->className ?? 'Mapper' . hash('sha256', random_bytes(1024));

        if (file_exists($context->path)) {
            include $context->path;
        }

        $fqcn = $namespace . $className;
        if (class_exists($fqcn, true)) {
            return new $fqcn();
        }

        $tree = $this->buildTree(
            $namespace,
            $className,
            ...$context->mappers
        );

        $prettyPrinter = new PrettyPrinter\Standard();
        if ($context->path !== null && is_writable(dirname($context->path))) {
            file_put_contents($context->path, $prettyPrinter->prettyPrintFile($tree));
            include $context->path;
        } else {
            include 'data://text/plain;base64,' . base64_encode($prettyPrinter->prettyPrintFile($tree));
        }

        return new $fqcn();
    }

    public function buildTree(string $namespace, string $className, CompilableMapperInterface ...$mappers): array
    {
        $trees = [];
        foreach ($mappers as $mapper) {
            $trees = array_merge(
                $trees,
                $mapper->compile()
            );
        }

        $factory = new BuilderFactory();

        return [
            $factory->namespace(rtrim($namespace, '\\'))
//                ->addStmt($factory->use(MapperInterface::class))
                ->addStmt($factory->class($className)
                    ->implement(new Node\Name\FullyQualified(MapperInterface::class))
                    ->makeFinal()
                    ->addStmt($factory->method('map')
                        ->makePublic()
                        ->setReturnType('array')
                        ->addParam($factory->param('input')->setType('array'))
                        ->addStmt(new Node\Stmt\Return_(
                            new Node\Expr\Array_($trees)
                        ))
                    )
                )
                ->getNode()
        ];
    }
}
