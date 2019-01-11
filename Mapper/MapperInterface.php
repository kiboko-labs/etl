<?php

namespace Kiboko\Component\ETL\Mapper;

interface MapperInterface
{
    public function map(array $input): array;
}
