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

use Swow\Psr7\Server\Server as Psr7Server;
use Swow\Socket;

class Constant
{
    public const ENGINE = 'Swow';

    public static function isCoroutineServer($server): bool
    {
        // Since Swow 2.x, the PSR-7 HTTP server no longer extends `Swow\Socket`.
        return $server instanceof Socket || $server instanceof Psr7Server;
    }
}
