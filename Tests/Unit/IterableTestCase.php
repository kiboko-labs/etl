<?php

namespace Kiboko\Component\ETL\Tests\Unit;

use PHPUnit\Framework\TestCase;

abstract class IterableTestCase extends TestCase
{
    protected function assertIteration(\Traversable $expected, \Traversable $actual, $message = '')
    {
        if ($message) {
            $message .= "\n";
        }

        $expected = $expected instanceof \Iterator ? $expected : new \IteratorIterator($expected);
        $actual   = $actual instanceof \Iterator ? $actual : new \IteratorIterator($actual);

        $both = new \MultipleIterator(\MultipleIterator::MIT_NEED_ALL);
        $both->attachIterator($expected);
        $both->attachIterator($actual);

        $index = 0;
        foreach ($both as list($leftValue, $rightValue)) {
            ++$index;
            list($leftKey, $rightKey) = $both->key();
            $this->assertSame($leftValue, $rightValue, sprintf("%sValues of Iteration #%d", $message, $index));
            $this->assertSame($leftKey, $rightKey, sprintf("%sKeys of Iteration #%d", $message, $index));
        }

        $this->assertFalse($expected->valid(), sprintf("%sCount mismatch: Expected Iterator still valid (#%d)", $message, $index));
        $this->assertFalse($actual->valid(), sprintf("%sCount mismatch: Actual Iterator still valid (#%d)", $message, $index));
    }

    protected function assertIterationValues(array $expected, \Traversable $actual, $message = '')
    {
        $actual = iterator_to_array($actual, FALSE);
        $this->assertEquals($expected, $actual, $message);
    }
}
