<?php

namespace Kiboko\Component\ETL\Pipeline;

use M6Web\Tornado\EventLoop;

class EventLoopPipelineRunner implements PipelineRunnerInterface
{
    /**
     * @var EventLoop
     */
    private $eventLoop;

    /**
     * @param EventLoop $eventLoop
     */
    public function __construct(EventLoop $eventLoop)
    {
        $this->eventLoop = $eventLoop;
    }

    public function fork(callable $reduce, \Iterator $source, \Generator ...$async): \Iterator
    {
        // TODO: Implement fork() method.
    }

    public function await(callable $reduce, \Iterator $source, \Generator ...$async): iterable
    {
        // TODO: Implement await() method.
    }

    public function pipe(\Iterator $source, \Generator ...$async): \Iterator
    {
        // TODO: Implement pipe() method.
    }

    public function run(\Iterator $source, \Generator $async): \Iterator
    {
        // TODO: Implement run() method.
    }
}
