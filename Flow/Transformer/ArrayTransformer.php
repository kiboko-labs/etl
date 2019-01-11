<?php

namespace Kiboko\Component\ETL\Flow\Transformer;

use Kiboko\Component\ETL\Mapper\MapperInterface;

class ArrayTransformer implements TransformerInterface
{
    /**
     * @var MapperInterface[]
     */
    private $mappers;

    /**
     * @param MapperInterface[] $mappers
     */
    public function __construct(MapperInterface... $mappers)
    {
        $this->mappers = $mappers;
    }

    private function walkMappers(array $input): \Generator
    {
        foreach ($this->mappers as $mapper) {
            yield $mapper->map($input);
        }
    }

    public function transform(): \Generator
    {
        while ($line = yield) {
            yield array_merge(...$this->walkMappers($line));
        }
    }
}
