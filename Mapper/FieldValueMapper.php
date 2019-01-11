<?php

namespace Kiboko\Component\ETL\Mapper;

class FieldValueMapper implements MapperInterface
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
            $this->outputField => $input[$this->inputField],
        ];
    }
}
