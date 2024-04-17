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

use Hyperf\Engine\Contract\Http\Http as HttpContract;
use Stringable;
use Swow\Http\Http as SwowHttp;

class Http implements HttpContract
{
    public static function packRequest(string $method, string|Stringable $path, array $headers = [], string|Stringable $body = '', string $protocolVersion = HttpContract::DEFAULT_PROTOCOL_VERSION): string
    {
        return SwowHttp::packRequest(
            $method,
            $path,
            $headers,
            $body,
            $protocolVersion
        );
    }

    public static function packResponse(int $statusCode, string $reasonPhrase = '', array $headers = [], string|Stringable $body = '', string $protocolVersion = HttpContract::DEFAULT_PROTOCOL_VERSION): string
    {
        return SwowHttp::packResponse(
            $statusCode,
            $reasonPhrase,
            $headers,
            $body,
            $protocolVersion
        );
    }
}
