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
! defined('SWOOLE_SOCK_TCP6') && define('SWOOLE_SOCK_TCP6', Socket::TYPE_TCP6);
! defined('SWOOLE_SOCK_UDP') && define('SWOOLE_SOCK_UDP', Socket::TYPE_UDP);
! defined('SWOOLE_SOCK_UDP6') && define('SWOOLE_SOCK_UDP6', Socket::TYPE_UDP6);
