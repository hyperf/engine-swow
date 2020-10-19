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
use function Swow\Http\packRequest;

$socket = new Swow\Socket();
$socket->connect('127.0.0.1', 9501);
$socket->write([packRequest('GET', '/')]);
$socket->recv($buffer = new Swow\Buffer());
var_dump($buffer->rewind()->getContents());
