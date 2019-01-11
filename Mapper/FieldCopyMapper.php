<?php

namespace Kiboko\Component\ETL\Mapper;

class FieldCopyMapper implements MapperInterface
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
}
