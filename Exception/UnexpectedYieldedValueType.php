<?php

namespace Kiboko\Component\ETL\Exception;

class UnexpectedYieldedValueType extends \UnexpectedValueException
{
    /**
     * @var \Generator
     */
    private $coroutine;

    /**
     * @param \Generator $coroutine
     */
    public function __construct(\Generator $coroutine, string $message = null, int $code = null, ?\Exception $previous = null)
    {
        $this->coroutine = $coroutine;
        parent::__construct($message, $code, $previous);
    }

    public static function expectingType(\Generator $coroutine, string $expectedType, $actual, int $code = null, ?\Exception $previous = null): self
    {
        $re = new \ReflectionGenerator($coroutine);

        $function = $re->getFunction();
        $functionName = $function->getName();

        if ($function instanceof \ReflectionMethod) {
            $class = $function->getDeclaringClass();
            $functionName = $class->getName() . '::' . $functionName;
        }

        return new self($coroutine, strtr(
            'Invalid yielded data, was expecting %expected%, got %actual%. Coroutine declared in %function%, running in %file%:%line%.',
            [
                '%expected%' => $expectedType,
                '%actual%' => is_object($actual) ? get_class($actual) : gettype($actual),
                '%function%' => $functionName,
                '%file%' => $re->getExecutingFile(),
                '%line%' => $re->getExecutingLine(),
            ],
            $code,
            $previous
        ));
    }
}
