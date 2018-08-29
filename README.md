Kiboko ETL component
====================

Introduction
------------

An ETL is a design pattern aimed at synchronization routines on large scale volumes of data.
This library implements this concept in PHP with the help of `Iterator`s and `Generator`s.

Definitions
-----------

### Extractor

_Interface_: `Kiboko\Component\ETL\Extractor\ExtractorInterface`

An extractor will be the main data source

### Transformer

_Interface_: `Kiboko\Component\ETL\Transformer\TransformerInterface`

An transformer will be the intermediate which will change the format of the data

### Loader

_Interface_: `Kiboko\Component\ETL\Loader\LoaderInterface`

An loader will be the data sink, the place where the resulting data will be persisted

### Pipeline

A Pipeline is the helper class for associating each piece of software of the ETL and then make it run.

Examples
--------

A simple example of ETL pipeline implementation

```php
<?php

use Kiboko\Component\ETL\Extractor;
use Kiboko\Component\ETL\Transformer;
use Kiboko\Component\ETL\Loader;
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

A more complex example could be listed as the following code:

```php
<?php

use Kiboko\Component\ETL\Extractor;
use Kiboko\Component\ETL\Transformer;
use Kiboko\Component\ETL\Loader;
use Kiboko\Component\ETL\Pipeline;

$extractor = new Extractor\IteratorExtractor((function(){
    for ($i = 0; $i < 10000; ++$i) {
        yield [
            'id' => $i,
            'foo' => '    ' . base64_encode(random_bytes(22)) . '    ',
            'bar' => random_int(PHP_INT_MIN, PHP_INT_MAX),
        ];
    }
})());

class MyDTO
{
    public $id;
    public $foo;
    public $bar;
    public $baz;
    public $pid;

    /**
     * @param $id
     * @param $foo
     * @param $bar
     * @param $baz
     * @param $pid
     */
    public function __construct($id, $foo, $bar, $baz, $pid)
    {
        $this->id = $id;
        $this->foo = $foo;
        $this->bar = $bar;
        $this->baz = $baz;
        $this->pid = $pid;
    }
}

$pipeline = new Pipeline\Pipeline(
    new Pipeline\PipelineRunner()
);

$pipeline
    ->extract($extractor)
    ->transform(new Transformer\ColumnTrimTransformer(['foo']))
    ->load(new Loader\SplCSVLoader(new SplFileObject(__DIR__ . '/first-output-file.csv', 'w')))
    ->transform(new Transformer\CallableTransformer(function($line) {
        $line['baz'] = base64_encode(random_bytes(6));

        $line['pid'] = posix_getpid();
        return $line;
    }))
    ->transform(new Transformer\DenormalizationTransformer(
        new class implements \Symfony\Component\Serializer\Normalizer\DenormalizerInterface {
            public function denormalize($data, $class, $format = null, array $context = [])
            {
                return new MyDTO(
                    $data['id'],
                    $data['foo'],
                    $data['bar'],
                    $data['baz'],
                    $data['pid']
                );
            }

            public function supportsDenormalization($data, $type, $format = null)
            {
                return $type === MyDTO::class;
            }
        },
        MyDTO::class
    ))
    ->load(new Loader\SplCSVLoader(new SplFileObject(__DIR__ . '/second-output-file.csv', 'w')))
    ->load(new Loader\StdoutLoader())
    ->run();

```
