<?php

const AMP_DEBUG = true;

require __DIR__ . '/../../../../vendor/autoload.php';

use Kiboko\Component\ETL\Extractor;
use Kiboko\Component\ETL\Transformer;
use Kiboko\Component\ETL\Loader;
use Kiboko\Component\ETL\Pipeline;

class Log {
    public static $logger;
}

Log::$logger = new \Monolog\Logger('debug', [
    new Monolog\Handler\StreamHandler('php://stderr')
]);

/*
$extractor = new Extractor\ArrayExtractor([
    ['a', 1],
    ['b', 2],
    ['c', 3],
    ['d', 4],
]);
//*/
$extractor = new Extractor\IteratorExtractor((function(){
    for ($i = 0; $i < 12; ++$i) {
        yield [
            'id' => $i,
            'foo' => '    ' . base64_encode(random_bytes(22)) . '    ',
            'bar' => random_int(PHP_INT_MIN, PHP_INT_MAX),
        ];
    }
})());
//*/

$pipeline = new \Kiboko\Component\ETL\Pipeline\Pipeline(
    new \Kiboko\Component\ETL\Pipeline\PipelineRunner()
);
$pipeline
    ->extract($extractor)
    ->transform(new Transformer\ColumnTrimTransformer(['foo']))
    //->load(new Loader\SplCSVLoader(new SplFileObject(__DIR__ . '/test.csv', 'w')))
    ->fork(
        function($current, $next) {
            if ($current === null) {
                return $next;
            }
            if ($next === null) {
                return $current;
            }

            return array_merge($current, $next);
        },
//        new \Kiboko\Component\ETL\Tests\FooForkBuilder('foo.csv'),
        new \Kiboko\Component\ETL\Tests\BarForkBuilder('bar.csv')
    )
    ->transform(new Transformer\CallableTransformer(function($line) {
        $line['baz'] = base64_encode(random_bytes(6));

        $line['pid'] = posix_getpid();
        return $line;
    }))
    ->map(new \Kiboko\Component\ETL\Mapper\Map())
//    ->load(new Loader\SplCSVLoader(new SplFileObject(__DIR__ . '/test2.csv', 'w')))
    ->load(new Loader\StdoutLoader())
    ->run();

