Extractors
==========

* Read from an [`array`](#arrayextractor)
* Read from an [`Iterator`](#iteratorextractor)
* Read from a [*LD JSON* stream](#ldjsonextractor)
* Read from a [*SQL* database](#pdoextractor)
* Read from a *CSV* file
  * using [the SPL](#splcsvextractor)
  * using [`box/spout` readers](#spoutcsvextractor)
  * using [`box/spout` sheets](#spoutsheetextractor)
* Read from an *Excel* (.xlsx)file
  * using [`box/spout` sheets](#spoutsheetextractor)
* Read from an *OpenDocument* stylesheet (.ods) file
  * using [`box/spout` sheets](#spoutsheetextractor)

`ArrayExtractor`
----------------

Use the array extractor in order to read data from a PHP array, 
where each item will be considered as a line.

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

Use the iterator extractor to read data from an iterator,
each item will be considered as a line.

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

Read line delimited JSON files using the SPL,
each line of the LD JSON will be considered as a line.

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

Read data from a PDO statement result,
in other words run SQL queries each result will be considered as a line.

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

Read CSV files using the SPL,
each line of the CSV will be considered as a line.

The first line will be considered as holding the field names for the whole file.

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

Read CSV files using the `box/spout component,
each line of the CSV will be considered as a line.

The first line will be considered as holding the field names for the whole file.

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

Use `box/spout` component to read spreadsheets including CSV, Excel and ODS files,
each line of the sheet will be considered as a line.

The 2nd parameter of the constructor can determine the number of lines to skip, if needed.

The next line will be considered as holding the field names for the whole file.

```php
<?php
use Kiboko\Component\ETL\Flow\Extractor;
use Kiboko\Component\ETL\Pipeline\PipelineInterface;

/** @var PipelineInterface $pipeline */

/** @var \Box\Spout\Reader\SheetInterface $sheet */
$pipeline
    ->extract(new Extractor\SpoutSheetExtractor($sheet));
```
