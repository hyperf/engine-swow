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
! defined('SWOOLE_HOOK_ALL') && define('SWOOLE_HOOK_ALL', 0);
! defined('SWOOLE_BASE') && define('SWOOLE_BASE', 0);

if (class_exists(\Swow\Signal::class)){
  ! defined('SIGHUP') && define('SIGHUP',\Swow\Signal::HUP);
  ! defined('SIGINT') && define('SIGINT',\Swow\Signal::INT);
  ! defined('SIGQUIT') && define('SIGQUIT',\Swow\Signal::QUIT);
  ! defined('SIGILL') && define('SIGILL',\Swow\Signal::ILL);
  ! defined('SIGTRAP') && define('SIGTRAP',\Swow\Signal::TRAP);
  ! defined('SIGABRT') && define('SIGABRT',\Swow\Signal::ABRT);
  ! defined('SIGIOT') && define('SIGIOT',\Swow\Signal::IOT);
  ! defined('SIGBUS') && define('SIGBUS',\Swow\Signal::BUS);
  ! defined('SIGFPE') && define('SIGFPE',\Swow\Signal::FPE);
  ! defined('SIGKILL') && define('SIGKILL',\Swow\Signal::KILL);
  ! defined('SIGUSR1') && define('SIGUSR1',\Swow\Signal::USR1);
  ! defined('SIGSEGV') && define('SIGSEGV',\Swow\Signal::SEGV);
  ! defined('SIGUSR2') && define('SIGUSR2',\Swow\Signal::USR2);
  ! defined('SIGPIPE') && define('SIGPIPE',\Swow\Signal::PIPE);
  ! defined('SIGALRM') && define('SIGALRM',\Swow\Signal::ALRM);
  ! defined('SIGTERM') && define('SIGTERM',\Swow\Signal::TERM);
  ! defined('SIGSTKFLT') && define('SIGSTKFLT',\Swow\Signal::STKFLT);
  ! defined('SIGCLD') && define('SIGCLD',\Swow\Signal::CHLD);
  ! defined('SIGCHLD') && define('SIGCHLD',\Swow\Signal::CHLD);
  ! defined('SIGCONT') && define('SIGCONT',\Swow\Signal::CONT);
  ! defined('SIGSTOP') && define('SIGSTOP',\Swow\Signal::STOP);
  ! defined('SIGTSTP') && define('SIGTSTP',\Swow\Signal::TSTP);
  ! defined('SIGTTIN') && define('SIGTTIN',\Swow\Signal::TTIN);
  ! defined('SIGTTOU') && define('SIGTTOU',\Swow\Signal::TTOU);
  ! defined('SIGURG') && define('SIGURG',\Swow\Signal::URG);
  ! defined('SIGXCPU') && define('SIGXCPU',\Swow\Signal::XCPU);
  ! defined('SIGXFSZ') && define('SIGXFSZ',\Swow\Signal::XFSZ);
  ! defined('SIGVTALRM') && define('SIGVTALRM',\Swow\Signal::VTALRM);
  ! defined('SIGPROF') && define('SIGPROF',\Swow\Signal::PROF);
  ! defined('SIGWINCH') && define('SIGWINCH',\Swow\Signal::WINCH);
  ! defined('SIGPOLL') && define('SIGPOLL',\Swow\Signal::IO);
  ! defined('SIGIO') && define('SIGIO',\Swow\Signal::IO);
  ! defined('SIGPWR') && define('SIGPWR',\Swow\Signal::PWR);
  ! defined('SIGSYS') && define('SIGSYS',\Swow\Signal::SYS);
  ! defined('SIGBABY') && define('SIGBABY',\Swow\Signal::SYS);
}
