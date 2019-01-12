<?php

namespace Kiboko\Component\ETL\Mapper;

use PhpParser\BuilderFactory;
use PhpParser\Node;

class Compiler
{
    public function compile(CompilableMapperInterface ...$mappers): array
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
            $factory->namespace($namespace = 'Kiboko\\Component\\ETL\\Compiled')
                ->addStmt($factory->use(MapperInterface::class))
                ->addStmt($factory->class($className = 'Mapper' . hash('sha256', random_bytes(1024)))
                    ->implement(MapperInterface::class)
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
