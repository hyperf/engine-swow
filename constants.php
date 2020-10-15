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
use Swow\Socket;

! defined('SWOOLE_HOOK_ALL') && define('SWOOLE_HOOK_ALL', 0);
! defined('SWOOLE_BASE') && define('SWOOLE_BASE', 0);
! defined('SWOOLE_SOCK_TCP') && define('SWOOLE_SOCK_TCP', Socket::TYPE_TCP);

// var_dump(\Swow\Coroutine::getCurrent()->getId());

// var_dump(\Swoole\Coroutine::getCid());
// \Swoole\Coroutine\Run(function () {
//     var_dump(\Swoole\Coroutine::getPcid());
// });
