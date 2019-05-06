<?php

namespace Kiboko\Component\ETL\Iterator;

class ResumableIterator implements \Iterator
{
    /**
     * @var \Closure
     */
    private $generator;

    /**
     * @var \Iterator
     */
    private $source;

    /**
     * @var \Iterator
     */
    private $inner;

    /**
     * @param \Closure $generator
     * @param \Iterator $source
     */
    public function __construct(\Closure $generator, \Iterator $source)
    {
        $this->generator = $generator;
        $this->source = $source;
        $this->reset();
    }

    public function current()
    {
        $this->resume();
        return $this->inner->current();
    }

    public function next()
    {
        $this->resume();
        $this->inner->next();
    }

    public function key()
    {
        $this->resume();
        return $this->inner->key();
    }

    public function valid()
    {
        $this->resume();
        return $this->inner->valid();
    }

    public function rewind()
    {
        $this->reset();
    }

    private function reset()
    {
        $this->inner = ($this->generator)($this->source);
    }

    private function resume()
    {
        if (!$this->inner->valid() && $this->source->valid()) {
            $this->reset();
        }
    }
}
