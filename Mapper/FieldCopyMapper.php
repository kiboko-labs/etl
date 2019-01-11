<?php

namespace Kiboko\Component\ETL\Mapper;

class FieldCopyMapper implements MapperInterface
{
    /**
     * @var string
     */
    private $inputField;

    /**
     * @var string
     */
    private $outputField;

    /**
     * @param string $outputField
     * @param string $inputField
     */
    public function __construct(string $inputField, string $outputField)
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
