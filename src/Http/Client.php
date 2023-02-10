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
use Swow\Psr7\Client\Client as HttpClient;

class Client extends HttpClient implements ClientInterface
{
    protected string $host;

    protected int $port;

    private bool $ssl;

    public function __construct(string $name, int $port, bool $ssl = false)
    {
        parent::__construct();
        $this->host = $name;
        $this->port = $port;
        $this->ssl = $ssl;
    }

    public function set(array $settings): bool
    {
        // Set settings
        if (isset($settings['timeout'])) {
            $this->setReadTimeout(intval($settings['timeout'] * 1000));
        }

        return true;
    }

    /**
     * @param string[][] $headers
     */
    public function request(string $method = 'GET', string $path = '/', array $headers = [], string $contents = '', string $version = '1.1'): RawResponse
    {
        if (! $this->isEstablished()) {
            $this->connect($this->host, $this->port);
            if ($this->ssl) {
                $this->enableCrypto();
            }
        }

        $headers = array_change_key_case($headers, CASE_LOWER);
        if (! isset($headers['content-length'])) {
            $headers['content-length'] = strlen($contents);
        }
        $this->sendPackedRequestAsync($method, $path, $headers, $contents, $version);
        $result = $this->recvResponseEntity();
        return new RawResponse(
            $result->statusCode,
            $result->headers,
            (string) $result->body,
            $result->protocolVersion
        );
    }
}
