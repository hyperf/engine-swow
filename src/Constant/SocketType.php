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
namespace Hyperf\Engine\Constant;

use Swow\Socket;

class SocketType
{
    public const TCP = Socket::TYPE_TCP;

    public const TCP6 = Socket::TYPE_TCP6;

    public const UDP = Socket::TYPE_UDP;

    public const UDP6 = Socket::TYPE_UDP6;

    public const UNIX_STREAM = Socket::TYPE_UNIX;

    public const UNIX_DGRAM = Socket::TYPE_UDG;
}
