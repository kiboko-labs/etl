Extractors
==========

`ArrayExtractor`
----------------
Red data from a PHP array.

```php
<?php
use Kiboko\Component\ETL\Flow\Extractor;
use Kiboko\Component\ETL\Pipeline\PipelineInterface;

/** @var PipelineInterface $pipeline */

$pipeline
    ->extract(new Extractor\ArrayExtractor([
        ['foo' => '   a ', 'bar' => 1],
        ['foo' => '   b ', 'bar' => 2],
        ['foo' => '   c ', 'bar' => 3],
        ['foo' => '   d ', 'bar' => 4],
    ]));
```

`IteratorExtractor`
-------------------

Read data from an iterator.

```php
<?php
use Kiboko\Component\ETL\Flow\Extractor;
use Kiboko\Component\ETL\Pipeline\PipelineInterface;

/** @var PipelineInterface $pipeline */

$pipeline
    ->extract(new Extractor\IteratorExtractor(
        new \GlobIterator('*.php')
    ));
```

`LDJSONExtractor`
-----------------

Read line delimited JSON files using the SPL.

```php
<?php
use Kiboko\Component\ETL\Flow\Extractor;
use Kiboko\Component\ETL\Pipeline\PipelineInterface;

/** @var PipelineInterface $pipeline */

$pipeline
    ->extract(new Extractor\LDJSONExtractor(
        new \SplFileObject('source.ldjson', 'r')
    ));
```

`PDOExtractor`
--------------

Read data from a PDO statement result.

```php
<?php
use Kiboko\Component\ETL\Flow\Extractor;
use Kiboko\Component\ETL\Pipeline\PipelineInterface;

/** @var PipelineInterface $pipeline */

$connection = new \PDO('sqlite::memory:');

$pipeline
    ->extract(new Extractor\PDOExtractor(
        $connection->prepare('SELECT * FROM products WHERE updated>?'),
        [
            (new DateTimeImmutable('now'))->format('c')
        ]
    ));
```

`SplCSVExtractor`
-----------------

Read CSV files using the SPL.

```php
<?php
use Kiboko\Component\ETL\Flow\Extractor;
use Kiboko\Component\ETL\Pipeline\PipelineInterface;

/** @var PipelineInterface $pipeline */
$pipeline
    ->extract(new Extractor\SplCSVExtractor(
        new \SplFileObject('source.csv', 'r'), ';', '"', '"'
    ));
```

`SpoutCsvExtractor`
-------------------

Use `box/spout` component to read CSV files with no abstraction.

```php
<?php
use Kiboko\Component\ETL\Flow\Extractor;
use Kiboko\Component\ETL\Pipeline\PipelineInterface;

/** @var PipelineInterface $pipeline */

/** @var \Box\Spout\Reader\CSV\Reader $sheet */
$pipeline
    ->extract(new Extractor\SpoutCsvExtractor($sheet));
```

`SpoutSheetExtractor`
---------------------

Use `box/spout` component to read CSV, Excel and ODS files.

```php
<?php
use Kiboko\Component\ETL\Flow\Extractor;
use Kiboko\Component\ETL\Pipeline\PipelineInterface;

/** @var PipelineInterface $pipeline */

/** @var \Box\Spout\Reader\SheetInterface $sheet */
$pipeline
    ->extract(new Extractor\SpoutSheetExtractor($sheet));
```
