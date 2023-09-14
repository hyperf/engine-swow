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

namespace Hyperf\Engine;

use Psr\Log\LoggerInterface;
use Swow\Buffer;
use Swow\Socket;

class Server extends Socket
{
    public ?string $host = null;

    public ?int $port = null;

    /**
     * @var callable
     */
    protected $handler;

    public function __construct(protected LoggerInterface $logger, public int $type = Socket::TYPE_TCP)
    {
        parent::__construct($type);
    }

    public function bind(string $name, int $port = 0, int $flags = Socket::BIND_FLAG_NONE): static
    {
        $this->host = $name;
        $this->port = $port;
        parent::bind($name, $port, $flags);
        return $this;
    }

    public function handle(callable $callable)
    {
        $this->handler = $callable;
        return $this;
    }

    public function start()
    {
        switch ($this->type) {
            case Socket::TYPE_TCP:
                $this->listen();
                while (true) {
                    Coroutine::create($this->handler, $this->accept());
                }
                break;
            case Socket::TYPE_UDP:
                while (true) {
                    $data = $this->recvStringFrom(Buffer::COMMON_SIZE, $clinetinfo['address'], $clinetinfo['port']);
                    Coroutine::create($this->handler, $this, $data, $clinetinfo);
                }
                break;
        }
    }
}
