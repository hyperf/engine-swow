<?php

namespace Hyperf\Engine\Http;

use Hyperf\Engine\Contract\Http\ConnectionInterface;
use Hyperf\Engine\Contract\Http\Chunkable;

use Swow\Psr7\Server\ServerConnection;

class Connection implements ConnectionInterface
{
    public function __construct(protected ServerConnection $response)
    {
    }

    public function write(string $data): bool
    {
         $this->response->write([
            sprintf("%s\r\n%s\r\n", strlen($data), $data)
        ]);

        return true;
    }

    /**
     * @return ServerConnection 
     */
    public function getSocket(): mixed
    {
        return $this->response;
    }

    public function end(): void
    {
        $this->response->write(["0\r\n"]);
        $this->response->write(["\r\n"]);
    }
}
