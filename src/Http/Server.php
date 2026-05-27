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
use Swow\Process\ProcessManager;
use Swow\Process\WorkerContext;
use Swow\Psr7\Psr7;
use Swow\Psr7\Server\Server as HttpServer;
use Swow\Socket;
use Swow\SocketException;
use Throwable;

class Server extends HttpServer implements ServerInterface
{
    public ?string $host = null;

    public ?int $port = null;

    protected int $workerCount = 1;

    protected ?ProcessManager $processManager = null;

    /** @var array<int, Coroutine> */
    protected array $connectionCoroutineMap = [];

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

    /**
     * Set number of worker processes.
     * When > 1, the server uses prefork model: parent bind+listen, children accept.
     */
    public function setWorkerNum(int $count): static
    {
        $this->workerCount = max(1, $count);
        return $this;
    }

    public function getWorkerNum(): int
    {
        return $this->workerCount;
    }

    public function start(): void
    {
        $this->listen();

        if ($this->workerCount > 1) {
            $this->processManager = new ProcessManager($this->workerCount);
            $this->processManager->start(function (WorkerContext $ctx): void {
                $this->runAcceptLoop();
            });
        } else {
            $this->runAcceptLoop();
        }
    }

    public function close(): bool
    {
        if ($this->processManager !== null) {
            $this->processManager->stop();
            $this->processManager = null;
            return true;
        }

        return parent::close();
    }

    protected function runAcceptLoop(): void
    {
        while (true) {
            try {
                $connection = $this->acceptConnection();
                Coroutine::create(function () use ($connection) {
                    $this->connectionCoroutineMap[$connection->getId()] = Coroutine::getCurrent();
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
                        unset($this->connectionCoroutineMap[$connection->getId()]);
                        $connection->close();
                    }
                });
            } catch (CoroutineException|SocketException $exception) {
                if (in_array($exception->getCode(), [Errno::EMFILE, Errno::ENFILE, Errno::ENOMEM], true)) {
                    $this->logger->warning('Socket resources have been exhausted.');
                    sleep(1);
                } elseif ($exception->getCode() === Errno::ECANCELED) {
                    $this->logger->info('Socket accept has been canceled.');
                    break;
                } else {
                    $this->logger->error((string) $exception);
                    break;
                }
            } catch (Throwable $exception) {
                $this->logger->error((string) $exception);
            }
        }

        // Close coroutines that are accepting connections when server stop.
        // Don't worry about the unfinished application request. It's running in a new coroutine.
        foreach ($this->connectionCoroutineMap as $connectionId => $connectionCoroutine) {
            if ($connectionCoroutine->isAvailable()) {
                $connectionCoroutine->kill();
                unset($this->connectionCoroutineMap[$connectionId]);
            }
        }
    }
}
