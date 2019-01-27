<?php

namespace Kiboko\Component\ETL\Iterator;

use Monolog\Handler\NullHandler;
use Monolog\Logger;
use Psr\Log\LoggerInterface;

class IteratorLoggingWrapper implements \Iterator
{
    /**
     * @var \Iterator
     */
    private $wrapped;

    /**
     * @var LoggerInterface
     */
    private $logger;

    /**
     * @var \ReflectionGenerator|null
     */
    private $reflectionGenerator;

    /**
     * @var \ReflectionObject|null
     */
    private $reflectionObject;

    /**
     * @param \Iterator $wrapped
     * @param LoggerInterface $logger
     */
    public function __construct(\Iterator $wrapped, LoggerInterface $logger = null)
    {
        $this->wrapped = $wrapped;
        $this->logger = $logger ?? new Logger(null, [new NullHandler()]);

        if ($this->wrapped instanceof \Generator) {
            try {
                $this->reflectionGenerator = new \ReflectionGenerator($this->wrapped);
            } catch (\ReflectionException $e) {
                throw new \RuntimeException('An error occured during reflection.', null, $e);
            }
        }

        try {
            $this->reflectionObject = new \ReflectionObject($this->wrapped);
        } catch (\ReflectionException $e) {
            throw new \RuntimeException('An error occured during reflection.', null, $e);
        }
    }

    private function debug(string $calledMethod, \Iterator $iterator, $value = null)
    {
        try {
            $message = 'Wrapped %type%->%iter% [%object%]: ';
            if ($this->reflectionGenerator !== null) {
                $function = $this->reflectionGenerator->getFunction();
                $functionName = $function->getName();

                if ($function instanceof \ReflectionMethod) {
                    $class = $function->getDeclaringClass();
                    $functionName = $class->getName() . '::' . $function->getName();
                }

                $options = [
                    'iter' => $calledMethod,
                    'object' => spl_object_hash($this->wrapped),
                    'function' => var_export($functionName, true),
                    'file' => var_export($this->reflectionGenerator->getExecutingFile(), true),
                    'line' => var_export($this->reflectionGenerator->getExecutingLine(), true),
                    'type' => $this->wrapped instanceof \Generator ? 'generator' : 'iterator',
                ];
            } else {
                $message = 'Wrapped %type%->%iter% [%object%]: [terminated] ';

                $options = [
                    'iter' => $calledMethod,
                    'object' => spl_object_hash($this->wrapped),
                    'type' => $this->wrapped instanceof \Generator ? 'generator' : 'iterator',
                ];
            }
        } catch (\ReflectionException $e) {
            $message = 'Wrapped %type%->%iter% [%object%]: [terminated] ';

            $options = [
                'iter' => $calledMethod,
                'object' => spl_object_hash($this->wrapped),
                'type' => $this->wrapped instanceof \Generator ? 'generator' : 'iterator',
            ];
        }

        if (func_num_args() === 2) {
            $options = array_merge(
                $options,
                [
                    'value' => var_export($options, true),
                ]
            );
        }

        $parameters = [];
        $fields = [];
        foreach ($options as $key => $value) {
            if (!in_array($key, ['type', 'iter', 'object'])) {
                $fields[] = $key.'=%'.$key.'%';
                $parameters['%' . $key . '%'] = var_export($value, true);
            } else {
                $parameters['%' . $key . '%'] = $value;
            }
        }

        $message .= implode(', ', $fields);

        $this->logger->debug(strtr($message, $parameters));
    }

    public function current()
    {
        $current = $this->wrapped->current();

        $this->debug(__FUNCTION__, $this->wrapped, $current);

        return $current;
    }

    public function next()
    {
        $this->wrapped->next();

        $this->debug(__FUNCTION__, $this->wrapped);
    }

    public function key()
    {
        $key = $this->wrapped->key();

        $this->debug(__FUNCTION__, $this->wrapped, $key);

        return $key;
    }

    public function valid()
    {
        $valid = $this->wrapped->valid();

        $this->debug(__FUNCTION__, $this->wrapped);

        return $valid;
    }

    public function rewind()
    {
        $this->wrapped->rewind();

        $this->debug(__FUNCTION__, $this->wrapped);
    }
}
