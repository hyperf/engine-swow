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

use Swow\Socket;

class Constant
{
    const ENGINE = 'Swow';

    public static function isCoroutineServer($server): bool
    {
        return $server instanceof Socket;
    }
}
