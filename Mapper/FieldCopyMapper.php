<?php

namespace Kiboko\Component\ETL\Mapper;

use PhpParser\BuilderFactory;
use PhpParser\Node;

class FieldCopyMapper implements CompilableMapperInterface
{
    /**
     * @var string
     */
    private $outputField;

    /**
     * @var mixed
     */
    private $value;

    /**
     * @param string $outputField
     * @param mixed  $value
     */
    public function __construct(string $outputField, $value)
    {
        $this->outputField = $outputField;
        $this->value = $value;
    }

    public function map(array $input): array
    {
        return [
            $this->outputField => $this->value,
        ];
    }

    /**
     * @return Node[]
     */
    public function compile(): array
    {
        $builder = new BuilderFactory();

        return [
            new Node\Expr\ArrayItem(
                $builder->val($this->value),
                new Node\Scalar\String_($this->outputField)
            )
        ];
    }
}
