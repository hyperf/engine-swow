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
namespace Hyperf\Engine\Socket;

use Hyperf\Engine\Contract\Socket\SocketFactoryInterface;
use Hyperf\Engine\Contract\Socket\SocketOptionInterface;
use Hyperf\Engine\Contract\SocketInterface;
use Hyperf\Engine\Socket;
use Swow;

class SocketFactory implements SocketFactoryInterface
{
    public function make(SocketOptionInterface $option): SocketInterface
    {
        $socket = new Socket(Swow\Socket::TYPE_TCP);
        if ($protocol = $option->getProtocol()) {
            // TODO: Set Protocol
        }

        if ($option->getTimeout() === null) {
            $socket->connect($option->getHost(), $option->getPort());
        } else {
            $socket->connect($option->getHost(), $option->getPort(), intval($option->getTimeout() * 1000));
        }

        return $socket;
    }
}
