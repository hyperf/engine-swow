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
// $channel->pop(1);

\Swoole\Coroutine::create(function () {
    $channel = new \Swoole\Coroutine\Channel();
    $channel->pop(1);
});
