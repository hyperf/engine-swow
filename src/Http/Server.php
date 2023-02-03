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

use Hyperf\Engine\Contract\Http\ServerInterface;
use Hyperf\Engine\Coroutine;
use Psr\Log\LoggerInterface;
use Swow\CoroutineException;
use Swow\Errno;
use Swow\Http\Protocol\ProtocolException as HttpProtocolException;
use Swow\Psr7\Psr7;
use Swow\Psr7\Server\Server as HttpServer;
use Swow\Socket;
use Swow\SocketException;
use Throwable;

use function Swow\Sync\waitAll;

class Server extends HttpServer implements ServerInterface
{
    public ?string $host = null;

    public ?int $port = null;

    /**
     * @var callable
     */
    protected $handler;

    public function __construct(protected LoggerInterface $logger)
    {
        parent::__construct();
    }

    public function bind(string $name, int $port = 0, int $flags = Socket::BIND_FLAG_NONE): static
    {
        $this->host = $name;
        $this->port = $port;
        parent::bind($name, $port, $flags);
        return $this;
    }

    public function handle(callable $callable): static
    {
        $this->handler = $callable;
        return $this;
    }

    public function start(): void
    {
        $this->listen();
        Coroutine::create(function () {
            while (true) {
                try {
                    $connection = $this->acceptConnection();
                    Coroutine::create(function () use ($connection) {
                        try {
                            while (true) {
                                $request = null;
                                try {
                                    $request = $connection->recvHttpRequest();
                                    $handler = $this->handler;
                                    $handler($request, $connection);
                                } catch (HttpProtocolException $exception) {
                                    $connection->error($exception->getCode(), $exception->getMessage());
                                }
                                if (! $request || ! Psr7::detectShouldKeepAlive($request)) {
                                    break;
                                }
                            }
                        } catch (Throwable $exception) {
                            // $this->logger->critical((string) $exception);
                        } finally {
                            $connection->close();
                        }
                    });
                } catch (SocketException|CoroutineException $exception) {
                    if (in_array($exception->getCode(), [Errno::EMFILE, Errno::ENFILE, Errno::ENOMEM, Errno::ECANCELED], true)) {
                        $this->logger->warning('Socket resources have been exhausted.');
                        sleep(1);
                    } else {
                        $this->logger->error((string) $exception);
                        break;
                    }
                } catch (Throwable $exception) {
                    $this->logger->error((string) $exception);
                }
            }
        });

        waitAll();
    }
}
