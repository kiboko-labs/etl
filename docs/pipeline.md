Pipeline
========

Introduction
------------

A Pipeline is a suite of steps executed sequentially, to manage
data extractions, transformations and loading.

The pipeline object has a fluent interface and the resulting 
configuration can then be used as an `\Iterator` object.

The Pipeline class has a fluent interface.

Setup the Extractor
-------------------

The pipeline is consuming data provided by an extractor:

```php
<?php 
/** @var \Kiboko\Component\ETL\Pipeline\PipelineInterface $pipeline */
$pipeline->extract(new FooExtractor());
```

Add Transformers
----------------

The pipeline is consuming data provided by an extractor and will 
transform theem to be loaded in the sink storage:

```php
<?php 
/** @var \Kiboko\Component\ETL\Pipeline\PipelineInterface $pipeline */
$pipeline->transform(new FooTransformer());
```

Add Loaders
-----------

Once the data extracted and transformed, the loader will store the data
into the sink storage:

```php
<?php 
/** @var \Kiboko\Component\ETL\Pipeline\PipelineInterface $pipeline */
$pipeline->transform(new FooTransformer());
```

Run the pipeline
----------------

### Consume the whole dataset at once

```php
<?php

/** @var \Kiboko\Component\ETL\Pipeline\PipelineInterface $pipeline */
$pipeline->run();
```

### Iterate over the dataset

```php
<?php

/** @var \Kiboko\Component\ETL\Pipeline\PipelineInterface $pipeline */
foreach ($pipeline->walk() as $line) {
    // ...
}
```

Examples
--------

### Sanitize an array list

Read an array, trim specific columns and print to the console output

```php
<?php

use Kiboko\Component\ETL\Flow\Extractor;
use Kiboko\Component\ETL\Flow\Transformer;
use Kiboko\Component\ETL\Flow\Loader;
use Kiboko\Component\ETL\Pipeline;

$pipeline = new Pipeline\Pipeline(
    new Pipeline\PipelineRunner()
);

$pipeline
    ->extract(new Extractor\ArrayExtractor([
        ['foo' => '   a ', 1],
        ['foo' => '   b ', 'bar' => 2],
        ['foo' => '   c ', 'bar' => 3],
        ['foo' => '   d ', 'bar' => 4],
    ]))
    ->transform(new Transformer\ColumnTrimTransformer(['foo']))
    ->load(new Loader\StdoutLoader())
    ->run();

```

### Transform the contents of a CSV file into another file format

```php
<?php

use Kiboko\Component\ETL\Flow\Extractor;
use Kiboko\Component\ETL\Flow\Transformer;
use Kiboko\Component\ETL\Flow\Loader;
use Kiboko\Component\ETL\Pipeline;

$pipeline = new Pipeline\Pipeline(
    new Pipeline\PipelineRunner()
);

$pipeline
    ->extract(new Extractor\SplCSVExtractor(new SplFileObject(__DIR__ . '/input-file.csv', 'r')))
    ->transform(new Transformer\CallableTransformer(function($line) {
        return new Pipeline\GenericBucket([
            'title' => $line['Product name'],
            'identifier' => $line['SKU'],
            'public_price' => $line['Price Incl. Tax'],
        ]);
    }))
    ->load(new Loader\SplCSVLoader(new SplFileObject(__DIR__ . '/output-file.csv', 'w')))
    ->run();

```

### Move the contents of a CSV file into an ORM entity

```php
<?php

use Kiboko\Component\ETL\Flow\Extractor;
use Kiboko\Component\ETL\Flow\Transformer;
use Kiboko\Component\ETL\Flow\Loader;
use Kiboko\Component\ETL\Pipeline;

class Product
{
    public $id;
    public $sku;
    public $name;

    public function __construct(int $id, string $sku, string $name)
    {
        $this->id = $id;
        $this->sku = $sku;
        $this->name = $name;
    }
}

$pipeline = new Pipeline\Pipeline(
    new Pipeline\PipelineRunner()
);

$pipeline
    ->extract(new Extractor\SplCSVExtractor(new SplFileObject(__DIR__ . '/input-file.csv', 'r')))
    ->transform(new Transformer\ColumnTrimTransformer([
        'Identifier',
        'EAN',
        'Description'
    ]))
    ->transform(new Transformer\CallableTransformer(function($line){
        return new Pipeline\GenericBucket([
            'id' => $line['Identifier'],
            'sku' => $line['EAN'],
            'name' => $line['Description'],
        ]);
    }))
    ->transform(new Transformer\DenormalizationTransformer(
        new class implements \Symfony\Component\Serializer\Normalizer\DenormalizerInterface {
            public function denormalize($data, $class, $format = null, array $context = [])
            {
                return new Product(
                    $data['id'],
                    $data['sku'],
                    $data['name']
                );
            }

            public function supportsDenormalization($data, $type, $format = null)
            {
                return $type === Product::class;
            }
        },
        Product::class
    ))
    ->load(new Loader\PDOLoader(new PDO('sqlite::memory:')))
    ->run();

```
