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

use Hyperf\Engine\Contract\ChannelInterface;
use Swow\Channel as SwowChannel;
use Swow\ChannelException;

class Channel implements ChannelInterface
{
    private bool $succeeded = true;

    private SwowChannel $channel;

    public function __construct(int $capacity = 0)
    {
        $this->channel = new SwowChannel($capacity);
    }

    public function pop(float $timeout = -1): mixed
    {
        try {
            $this->succeeded = true;
            return $this->channel->pop($timeout == -1 ? -1 : intval($timeout * 1000));
        } catch (ChannelException) {
            $this->succeeded = false;
            return false;
        }
    }

    public function push(mixed $data, float $timeout = -1): bool
    {
        try {
            $this->succeeded = true;
            $this->channel->push($data, $timeout == -1 ? -1 : intval($timeout * 1000));
            return true;
        } catch (ChannelException) {
            $this->succeeded = false;
            return false;
        }
    }

    public function isTimeout(): bool
    {
        return ! $this->succeeded && $this->channel->isAvailable();
    }

    public function isClosing(): bool
    {
        return ! $this->channel->isAvailable();
    }

    public function close(): bool
    {
        $this->channel->close();
        return true;
    }

    public function getCapacity(): int
    {
        return $this->channel->getCapacity();
    }

    public function getLength(): int
    {
        return $this->channel->getLength();
    }

    public function isAvailable(): bool
    {
        return $this->channel->isAvailable();
    }

    public function hasProducers(): bool
    {
        return $this->channel->hasProducers();
    }

    public function hasConsumers(): bool
    {
        return $this->channel->hasConsumers();
    }

    public function isEmpty(): bool
    {
        return $this->channel->isEmpty();
    }

    public function isFull(): bool
    {
        return $this->channel->isFull();
    }

    public function isReadable(): bool
    {
        return $this->channel->isReadable();
    }

    public function isWritable(): bool
    {
        return $this->channel->isWritable();
    }
}
