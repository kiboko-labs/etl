<?php

namespace Kiboko\Component\ETL\Mapper;

class FieldConcatMapper implements MapperInterface
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
}
