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
    /**
     * @var string
     */
    protected $host;

    /**
     * @var int
     */
    protected $port;

    /**
     * TODO: Swow not support ssl.
     * @var bool
     */
    private $ssl = false;

    public function __construct(string $name, int $port, bool $ssl = false)
    {
        parent::__construct();
        $this->host = $name;
        $this->port = $port;
        $this->ssl = $ssl;
    }

    public function set(array $settings)
    {
        // Set settings
        if (isset($settings['timeout'])) {
            $this->setReadTimeout(intval($settings['timeout'] * 1000));
        }
        return $this;
    }

    /**
     * @param string[][] $headers
     */
    public function request(string $method = 'GET', string $path = '/', array $headers = [], string $contents = '', string $version = '1.1'): RawResponse
    {
        if (! $this->isEstablished()) {
            $this->connect($this->host, $this->port);
        }

        $headers = array_change_key_case($headers, CASE_LOWER);
        if (! isset($headers['content-length'])) {
            $headers['content-length'] = strlen($contents);
        }
        $this->sendRaw($method, $path, $headers, $contents, $version);
        $result = $this->recvRaw();
        return new RawResponse(
            $result->statusCode,
            $result->headers,
            (string) $result->body,
            $result->protocolVersion
        );
    }
}
