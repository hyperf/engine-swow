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

use Hyperf\Engine\Contract\SocketInterface;
use Swow;

class Socket extends Swow\Socket implements SocketInterface
{
    public function sendAll(string $data, float $timeout = 0): int|false
    {
        if ($timeout > 0) {
            $this->send($data, timeout: intval($timeout * 1000));
        } else {
            $this->send($data);
        }
        return strlen($data);
    }

    public function recvAll(int $length = 65536, float $timeout = 0): string|false
    {
        if ($timeout > 0) {
            return $this->readString($length, intval($timeout * 1000));
        }
        return $this->readString($length);
    }

    public function recvPacket(float $timeout = 0): string|false
    {
        return false;
    }

    public function close(): bool
    {
        parent::close();
        return true;
    }
}
