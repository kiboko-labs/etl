Kiboko ETL Documentation
========================

E... T... what?
---------------

An ETL is a design pattern aimed at synchronization routines on large scale volumes of data. The 3 letters stand for Extract-Transform-Load.

This library implements this concept in PHP with the help of `Iterator` and `Generator` objects.

Terminology
-----------

* *Data Source*: a data storage on which the pipeline will read the data the processing is about
* *Data Sink*: a data storage on which the pipeline will write the data that has been processed

* *Pipeline*: a suite of steps executed sequentially
* *Pipeline Step*: an unitary operation executed by a pipeline
* *Extract*: the pipeline step in charge of reading the raw data source
* *Transform*: the pipeline step in charge of transformation and validation operations of the data. It can perform lookup operations in a second-level data source
* *Load*: the pipeline step in charge of the data persistence in the data sink

* *Lookup*: a transformation step doing some data lookup into a secondary data source
* *Validate*: a transformation step doing some data format 
and integrity checks

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
    ->load(new \Acme\ETL\Loader\ORMLoader(new PDO('sqlite::memory:')))
    ->run();

```



