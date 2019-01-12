<?php

namespace Kiboko\Component\ETL\Mapper;

use PhpParser\BuilderFactory;
use PhpParser\Node;

class FieldConcatMapper implements CompilableMapperInterface
{
    /**
     * @var string
     */
    private $outputField;

    /**
     * @var string
     */
    private $glue;

    /**
     * @var string[]
     */
    private $inputFields;

    /**
     * @param string   $outputField
     * @param string   $glue
     * @param string[] $inputFields
     */
    public function __construct(string $outputField, string $glue, string... $inputFields)
    {
        $this->outputField = $outputField;
        $this->glue = $glue;
        $this->inputFields = $inputFields;
    }

    public function map(array $input): array
    {
        return [
            $this->outputField => implode($this->glue, iterator_to_array($this->walkFields($input))),
        ];
    }

    public function walkFields(array $input): \Generator
    {
        foreach ($this->inputFields as $field) {
            yield $input[$field];
        }
    }

    /**
     * @return Node[]
     */
    public function compile(): array
    {
        $builder = new BuilderFactory();

        $values = [];

        $it = new \ArrayIterator($this->inputFields);
        $it->rewind();

        if ($it->valid()) {
            while (true) {
                $values[] = new Node\Expr\ArrayDimFetch(
                    new Node\Expr\Variable('input'),
                    new Node\Scalar\String_($it->current())
                );

                $it->next();
                if (!$it->valid()) {
                    break;
                }

                $values[] = new Node\Scalar\String_($this->glue);
            }
        }

        return [
            new Node\Expr\ArrayItem(
                $builder->concat(...$values),
                new Node\Scalar\String_($this->outputField)
            )
        ];
    }
}
