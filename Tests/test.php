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
    for ($i = 0; $i < 10000; ++$i) {
        yield [
            'id' => $i,
            'foo' => '    ' . base64_encode(random_bytes(22)) . '    ',
            'bar' => random_int(PHP_INT_MIN, PHP_INT_MAX),
        ];
    }
})());
//*/

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

$pipeline = new \Kiboko\Component\ETL\Pipeline\Pipeline(
    new \Kiboko\Component\ETL\Pipeline\PipelineRunner()
);
$pipeline
    ->extract($extractor)
    ->transform(new Transformer\ColumnTrimTransformer(['foo']))
    ->load(new Loader\SplCSVLoader(new SplFileObject(__DIR__ . '/test.csv', 'w')))
    ->transform(new Transformer\CallableTransformer(function($line) {
        $line['baz'] = base64_encode(random_bytes(6));

        $line['pid'] = posix_getpid();
        return $line;
    }))
    /*
    ->transform(new \Kiboko\Component\ETL\Transformer\MapTransformer(
        new Transformer\Mapper\DenormalizationMapping(
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
        )
    ))
    */
    ->load(new Loader\SplCSVLoader(new SplFileObject(__DIR__ . '/test2.csv', 'w')))
//    ->load(new Loader\StdoutLoader())
    ->run();

var_dump(memory_get_peak_usage(true));
