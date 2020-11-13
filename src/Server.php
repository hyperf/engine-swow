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
use Swow\Socket;

class Server extends Socket
{
    /**
     * @var string
     */
    public $host;

    /**
     * @var int
     */
    public $port;

    /**
     * @var callable
     */
    protected $handler;

    /**
     * @var LoggerInterface
     */
    protected $logger;

    public function __construct(LoggerInterface $logger, int $type = Socket::TYPE_TCP)
    {
        $this->logger = $logger;
        parent::__construct($type);
    }

    public function bind(string $name, int $port = 0, int $flags = Socket::BIND_FLAG_NONE)
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
        $this->listen();
        while (true) {
            Coroutine::create($this->handler, $this->accept());
        }
    }
}
