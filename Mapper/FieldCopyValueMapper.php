<?php

namespace Kiboko\Component\ETL\Mapper;

use PhpParser\Node;

class FieldCopyValueMapper implements CompilableMapperInterface
{
    /**
     * @var string
     */
    private $outputField;

    /**
     * @var string
     */
    private $inputField;

    /**
     * @param string $outputField
     * @param string $inputField
     */
    public function __construct(string $outputField, string $inputField)
    {
        $this->outputField = $outputField;
        $this->inputField = $inputField;
    }

    public function map(array $input): array
    {
        return [
            $this->outputField => $input[$this->inputField] ?? null,
        ];
    }

    /**
     * @return Node[]
     */
    public function compile(): array
    {
        return [
            new Node\Expr\ArrayItem(
                new Node\Expr\BinaryOp\Coalesce(
                    new Node\Expr\ArrayDimFetch(
                        new Node\Expr\Variable('input'),
                        new Node\Scalar\String_($this->inputField)
                    ),
                    new Node\Expr\ConstFetch(
                        new Node\Name('null')
                    )
                ),
                new Node\Scalar\String_($this->outputField)
            )
        ];
    }
}
