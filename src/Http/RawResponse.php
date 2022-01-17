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

final class RawResponse
{
    /**
     * @param string[][] $headers
     */
    public function __construct(public int $statusCode, public array $headers, public string $body, public string $version)
    {
    }
}
