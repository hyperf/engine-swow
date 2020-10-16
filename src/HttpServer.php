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
use Swow\Http\Exception as HttpException;
use Swow\Http\Server;
use Swow\Socket;

class HttpServer extends Server
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

    protected $logger;

    public function __construct(LoggerInterface $logger)
    {
        $this->logger = $logger;
        parent::__construct();
    }

    public function bind(string $name, int $port = 0, int $flags = Socket::BIND_FLAG_NONE): Socket
    {
        $this->host = $name;
        $this->port = $port;
        parent::bind($name, $port, $flags);
        return $this;
    }

    public function handle(callable $callable)
    {
        $this->handler = $callable;
    }

    public function start()
    {
        $this->listen();
        Coroutine::create(function () {
            while (true) {
                try {
                    $session = $this->acceptSession();
                    Coroutine::create(function () use ($session) {
                        try {
                            while (true) {
                                $request = null;
                                try {
                                    $request = $session->recvHttpRequest();
                                    $handler = $this->handler;
                                    $handler($request, $session);
                                } catch (HttpException $exception) {
                                    $session->error($exception->getCode(), $exception->getMessage());
                                }
                                if (! $request || ! $request->getKeepAlive()) {
                                    break;
                                }
                            }
                        } catch (\Throwable $exception) {
                            // $this->logger->error((string) $exception);
                        } finally {
                            $session->close();
                        }
                    });
                } catch (\Throwable $exception) {
                    $this->logger->error((string) $exception);
                }
            }
        });
    }
}
