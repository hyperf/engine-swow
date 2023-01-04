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

use Exception;
use Hyperf\Contract\StdoutLoggerInterface;
use Hyperf\HttpServer\ResponseEmitter as Emitter;
use Psr\Http\Message\ResponseInterface;
use Swow\Psr7\Message\ResponsePlusInterface;
use Swow\Psr7\Psr7;
use Swow\Psr7\Server\ServerConnection;

class ResponseEmitter extends Emitter
{
    public function __construct(protected ?StdoutLoggerInterface $logger)
    {
    }

    /**
     * @param ResponseInterface|ResponsePlusInterface $response
     * @param ServerConnection $connection
     */
    public function emit(ResponseInterface $response, mixed $connection, bool $withContent = true): void
    {
        try {
            if ($connection->getProtocolType() === ServerConnection::PROTOCOL_TYPE_WEBSOCKET) {
                return;
            }

            Psr7::setHeaders($response, $response->getStandardHeaders());

            $connection->sendHttpResponse($response);
        } catch (Exception $exception) {
            $this->logger?->critical((string) $exception);
        }
    }
}
