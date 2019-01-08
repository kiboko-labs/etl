<?php

namespace Kiboko\Component\ETL\EventLoop;

use M6Web\Tornado\EventLoop;

class Loop
{
    /**
     * @var EventLoop
     */
    private $loop;

    /**
     * Loop constructor.
     * @param EventLoop $loop
     */
    public function __construct(EventLoop $loop)
    {
        $this->loop = $loop;
    }

    public function run()
    {
        $this->loop->async()

        Loop::run(function () {
            $uris = [
                "https://google.com/",
                "https://github.com/",
                "https://stackoverflow.com/",
            ];

            $client = new Amp\Artax\DefaultClient;
            $client->setOption(Amp\Artax\Client::OP_DISCARD_BODY, true);

            try {
                foreach ($uris as $uri) {
                    $promises[$uri] = $client->request($uri);
                }

                $responses = yield $promises;

                foreach ($responses as $uri => $response) {
                    print $uri . " - " . $response->getStatus() . $response->getReason() . PHP_EOL;
                }
            } catch (Amp\Artax\HttpException $error) {
                // If something goes wrong Amp will throw the exception where the promise was yielded.
                // The Client::request() method itself will never throw directly, but returns a promise.
                print $error->getMessage() . PHP_EOL;
            }
        });
    }
}
