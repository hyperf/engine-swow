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

use Hyperf\Engine\Contract\Http\ClientInterface;
use Swow\Http\Client as HttpClient;

class Client extends HttpClient implements ClientInterface
{
    public function __construct(string $name, int $port, bool $ssl = false)
    {
        parent::__construct($name, $port);
    }

    public function set(array $settings)
    {
        // TODO: 设置参数
        return $this;
    }

    /**
     * @param string[][] $headers
     */
    public function request(string $method = 'GET', string $path = '/', array $headers = [], string $conotents = '', string $version = '1.1'): RawResponse
    {
        $this->sendRawData($method, $path, $headers, $conotents, $version);
        $result = $this->recvRawData();
        return new RawResponse(
            $result->statusCode,
            $result->headers,
            (string) $result->body,
            $result->protocolVersion
        );
    }
}