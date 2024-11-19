<?php

declare(strict_types=1);
/**
 * This file is part of Hyperf.
 *
 * @link     https://www.hyperf.io
 * @document https://hyperf.wiki
 * @contact  group@hyperf.io
 * @license  https://github.com/hyperf/hyperf/blob/master/LICENSE
 */

namespace Hyperf\Engine\Http;

use Hyperf\Engine\Contract\Http\Writable;
use Swow\Psr7\Server\ServerConnection;

class WritableConnection implements Writable
{
    protected bool $sent = false;

    public function __construct(protected ServerConnection $response)
    {
    }

    public function write(string $data): bool
    {
        $this->response->write([
            sprintf("%s\r\n%s\r\n", dechex(strlen($data)), $data),
        ]);

        $this->sent = true;

        return true;
    }

    /**
     * @return ServerConnection
     */
    public function getSocket(): mixed
    {
        return $this->response;
    }

    public function end(): ?bool
    {
        $this->response->write(["0\r\n", "\r\n"]);

        $this->sent = true;

        return true;
    }

    public function isSent(): bool
    {
        return $this->sent;
    }
}
