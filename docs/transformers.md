Transformers
============

`ArrayTransformer`
------------------

Use the array transformer in order to transform
the source PHP array into another PHP array format.

This transformer works in combination with mappers.
Mappers are a transformation subset, it operates on a 
part of the source data and produces a part of the 
transformed data. The array transformer will run 
every mapper and combine the results of every mapper.

See [mappers chapter](mappers.md) in order to get details 
about how to use and combine mappers.

```php
<?php
use Kiboko\Component\ETL\Flow\Transformer;
use Kiboko\Component\ETL\Pipeline\PipelineInterface;

/** @var PipelineInterface $pipeline */

$pipeline
    ->transform(new Transformer\ArrayTransformer(
        new FooMapper(),
        new BarMapper(),
    ));
```

`BatchingTransformer`
---------------------

The batching transformer will group lines into batches
of lines the size of `$batchSize` constructor argument.

This is useful especially when working with API or ORM
sinks, as it is faster to send several objects to the 
database, but memory consumption uneffective if the amount
of objects sent is too large.

In the example below, the sink would receive arrays 
containing 100 lines each.

```php
<?php
use Kiboko\Component\ETL\Flow\Transformer;
use Kiboko\Component\ETL\Pipeline\PipelineInterface;

/** @var PipelineInterface $pipeline */

$pipeline
    ->transform(new Transformer\BatchingTransformer(100));
```

`CallableTransformer`
---------------------

`ChildPipelineTransformer`
--------------------------

```php
<?php
use Kiboko\Component\ETL\Flow\Transformer;
use Kiboko\Component\ETL\Pipeline\PipelineInterface;
use Kiboko\Component\ETL\Pipeline\PipelineRunnerInterface;

/** @var PipelineInterface $pipeline */
/** @var PipelineRunnerInterface $runner */

$pipeline
    ->transform(new Transformer\ChildPipelineTransformer(
        $runner,
        function(PipelineInterface $pipeline) {
            $pipeline
                ->transform(new FooTransformer())
                ->load(new FooLoader())
            ;
        }
    ));

```

`ColumnTrimTransformer`
-----------------------

`DenormalizationTransformer`
----------------------------

[See `NormalizationTransformer`](#normalizationtransformer)

`FilterTransformer`
-------------------

`ForkTransformer`
-----------------

`LookupTransformer`
-------------------

`NormalizationTransformer`
--------------------------

[See `DenormalizationTransformer`](#denormalizationtransformer)
