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

use Hyperf\Contract\ResponseEmitterInterface;
use Psr\Http\Message\ResponseInterface;
use Swow\Http\Server\Session;
use function Swow\Http\packResponse;

class ResponseEmitter implements ResponseEmitterInterface
{
    /**
     * @param Session $connection
     */
    public function emit(ResponseInterface $response, $connection, bool $withContent = true)
    {
        $connection->write([
            packResponse($response->getStatusCode(), $response->getHeaders()),
            $response->getBody()->getContents(),
        ]);
    }
}
