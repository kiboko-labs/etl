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

        $both = new \MultipleIterator(\MultipleIterator::MIT_NEED_ANY);
        $both->attachIterator($expected);
        $both->attachIterator($actual);

        $index = 0;
        foreach ($both as list($expectedItem, $actualItem)) {
            ++$index;
            //$this->assertTrue($actual->valid(), sprintf("%sActual iterator is closed, some items are missing after index #%d", $message, $index));
            //$this->assertTrue($expected->valid(), sprintf("%sExpected iterator is closed, some items are in excess after index #%d", $message, $index));
            $this->assertSame($expectedItem, $actualItem, sprintf("%sValues of Iteration #%d", $message, $index));
        }
    }
}
