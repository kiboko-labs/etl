<?php

namespace Kiboko\Component\ETL\Pipeline\Bucket;

class ComplexResultBucket implements
    AcceptanceResultBucketInterface,
    RejectionResultBucketInterface
{
    /**
     * @var RejectionResultBucketInterface[]
     */
    private $rejections;

    /**
     * @var AcceptanceResultBucketInterface[]
     */
    private $acceptances;

    /**
     * @param ResultBucketInterface[] $buckets
     */
    public function __construct(ResultBucketInterface... $buckets)
    {
        $this->accept(...array_filter(
            $buckets,
            function (ResultBucketInterface $bucket) {
                return $bucket instanceof AcceptanceResultBucketInterface;
            }
        ));

        $this->reject(...array_filter(
            $buckets,
            function (ResultBucketInterface $bucket) {
                return $bucket instanceof RejectionResultBucketInterface;
            }
        ));
    }

    public function accept(...$values): void
    {
        $this->acceptances[] = new AcceptanceResultBucket(...$values);
    }

    public function reject(...$values): void
    {
        $this->rejections[] = new RejectionResultBucket(...$values);
    }

    public function walkAcceptance(): iterable
    {
        $iterator = new \AppendIterator();
        foreach ($this->acceptances as $child) {
            /** @var array|\Traversable $acceptance */
            $acceptance = $child->walkAcceptance();
            if (is_array($acceptance)) {
                $iterator->append(new \ArrayIterator($acceptance));
                continue;
            }

            $iterator->append(new \IteratorIterator($acceptance));
        }

        return $iterator;
    }

    public function walkRejection(): iterable
    {
        $iterator = new \AppendIterator();
        foreach ($this->rejections as $child) {
            /** @var array|\Traversable $rejection */
            $rejection = $child->walkRejection();
            if (is_array($rejection)) {
                $iterator->append(new \ArrayIterator($rejection));
                continue;
            }

            $iterator->append(new \IteratorIterator($rejection));
        }

        return $iterator;
    }
}
