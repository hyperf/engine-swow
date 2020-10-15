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
use Swow\Channel\Exception;

class Channel extends \Swow\Channel implements ChannelInterface
{
    public function pop($timeout = -1)
    {
        try {
            return parent::pop($timeout == -1 ? -1 : intval($timeout * 1000));
        } catch (Exception $exception) {
            return false;
        }
    }

    public function isTimeout()
    {
        return $this->isAvailable();
    }

    public function isClosing(): bool
    {
        return ! $this->isAvailable();
    }
}
