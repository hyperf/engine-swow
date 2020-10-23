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

use Hyperf\Engine\Coroutine;
use Psr\Log\LoggerInterface;
use Swow\Coroutine\Exception as CoroutineException;
use Swow\Http\Exception as HttpException;
use Swow\Http\Server as HttpServer;
use Swow\Socket;
use Swow\Socket\Exception as SocketException;
use const Swow\Errno\EMFILE;
use const Swow\Errno\ENFILE;
use const Swow\Errno\ENOMEM;

class Server extends HttpServer
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
        return $this;
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
                } catch (SocketException | CoroutineException $exception) {
                    if (in_array($exception->getCode(), [EMFILE, ENFILE, ENOMEM], true)) {
                        $this->logger->warning('Socket resources have been exhausted.');
                        sleep(1);
                    } else {
                        $this->logger->error((string) $exception);
                        break;
                    }
                } catch (\Throwable $exception) {
                    $this->logger->error((string) $exception);
                }
            }
        });
    }
}
