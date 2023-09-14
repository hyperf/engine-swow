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

namespace Hyperf\Engine\WebSocket;

use Hyperf\Engine\Contract\WebSocket\FrameInterface;
use Hyperf\Engine\Exception\InvalidArgumentException;
use Swow\Psr7\Message\WebSocketFrame;

class Frame extends WebSocketFrame implements FrameInterface
{
    public static function from(mixed $frame): static
    {
        if (! $frame instanceof WebSocketFrame) {
            throw new InvalidArgumentException('The frame is invalid.');
        }

        return new Frame(
            $frame->getFin(),
            $frame->getRSV1(),
            $frame->getRSV2(),
            $frame->getRSV3(),
            $frame->getOpcode(),
            $frame->getPayloadLength(),
            $frame->getMaskingKey(),
            $frame->getPayloadData()
        );
    }
}
