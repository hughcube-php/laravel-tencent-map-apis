<?php
/**
 * Created by PhpStorm.
 * User: hugh.li
 * Date: 2021/4/20
 * Time: 4:19 下午.
 */

namespace HughCube\Laravel\Tencent\Map\Api;

use Closure;
use GuzzleHttp\HandlerStack;
use GuzzleHttp\RequestOptions;
use HughCube\GuzzleHttp\Client as HttpClient;
use HughCube\GuzzleHttp\HttpClientTrait;
use Illuminate\Support\Arr;
use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;

class Client
{
    use HttpClientTrait;

    protected $config;

    public function __construct(array $config)
    {
        $this->config = $config;
    }

    /**
     * @return array
     */
    protected function getKeys(): array
    {
        return $this->config['keys'] ?? [];
    }

    protected function createHttpClient(): HttpClient
    {
        $config = array_merge(
            ['base_uri' => 'https://apis.map.qq.com'],
            ($this->config['keys'] ?? [])
        );

        $config['handler'] = $handler = HandlerStack::create();
        $handler->push($this->appendKeyHandler());

        return new HttpClient($config);
    }

    protected function appendKeyHandler(): Closure
    {
        return function (callable $handler) {
            return function (RequestInterface $request, array $options) use ($handler) {
                $key = Arr::random($this->getKeys(), 1, false)[0];
                $options[RequestOptions::QUERY]['key'] = $key;

                return $handler($request, $options);
            };
        };
    }

    public function request(string $method, $uri, array $options = []): ResponseInterface
    {
        return $this->getHttpClient()->requestLazy(strtoupper($method), $uri, $options);
    }

    /**
     * @see https://lbs.qq.com/service/webService/webServiceGuide/webServiceSuggestion
     *
     * @param array $query
     * @param array $options
     *
     * @return ResponseInterface
     */
    public function suggestion(array $query, array $options = []): ResponseInterface
    {
        $options[RequestOptions::QUERY] = $query;

        return $this->request('GET', '/ws/place/v1/suggestion', $options);
    }
}
