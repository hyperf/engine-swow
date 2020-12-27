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
    protected $successed = true;

    public function pop($timeout = -1)
    {
        try {
            $this->successed = true;
            return parent::pop($timeout == -1 ? -1 : intval($timeout * 1000));
        } catch (Exception $exception) {
            $this->successed = false;
            return false;
        }
    }

    public function push($data, int $timeout = -1)
    {
        try {
            $this->successed = true;
            parent::push($data, $timeout == -1 ? -1 : intval($timeout * 1000));
            return true;
        } catch (Exception $exception) {
            $this->successed = false;
            return false;
        }
    }

    public function isTimeout()
    {
        return ! $this->successed && $this->isAvailable();
    }

    public function isClosing(): bool
    {
        return ! $this->isAvailable();
    }
}
