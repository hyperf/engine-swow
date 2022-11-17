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
namespace Hyperf\Engine\Http;

use Swow\Psr7\Server\ServerConnection as Connection;

class FdGetter
{
    public function get(Connection $response): int
    {
        return $response->getFd();
    }
}
